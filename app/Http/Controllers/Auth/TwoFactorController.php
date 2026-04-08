<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Show 2FA setup page
     */
    public function showSetup()
    {
        $company = Auth::guard('company')->user();

        if ($company->two_factor_enabled) {
            return redirect()->route('client.dashboard')
                ->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate secret key
        $secret = $this->google2fa->generateSecretKey();

        // Store temporarily in session
        session(['2fa_secret' => $secret]);

        // Generate QR code
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $company->email,
            $secret
        );

        return view('auth.setup-2fa', [
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $company = Auth::guard('company')->user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return back()->withErrors(['code' => 'Session expired. Please start the setup again.']);
        }

        // Verify the code
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid verification code. Please try again.']);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        // Enable 2FA
        $company->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        session()->forget('2fa_secret');

        return redirect()->route('client.dashboard')
            ->with('success', 'Two-factor authentication has been enabled successfully.')
            ->with('recovery_codes', $recoveryCodes);
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $company = Auth::guard('company')->user();

        if (!Hash::check($request->password, $company->password)) {
            return back()->withErrors(['password' => 'Invalid password.']);
        }

        $company->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show 2FA verification page (for login)
     */
    public function showVerification()
    {
        if (!session('2fa_required')) {
            return redirect()->route('login');
        }

        return view('auth.verify-2fa');
    }

    /**
     * Verify 2FA code during login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $companyId = session('2fa_company_id');

        if (!$companyId) {
            return redirect()->route('login')
                ->withErrors(['code' => 'Session expired. Please login again.']);
        }

        $company = Company::findOrFail($companyId);

        // Check if it's a recovery code
        $recoveryCodes = $company->two_factor_recovery_codes ?? [];
        if (in_array($request->code, $recoveryCodes)) {
            // Remove used recovery code
            $recoveryCodes = array_values(array_diff($recoveryCodes, [$request->code]));
            $company->update(['two_factor_recovery_codes' => $recoveryCodes]);

            Auth::guard('company')->login($company);
            $request->session()->regenerate();
            session()->forget(['2fa_required', '2fa_company_id']);

            return $this->redirectAfterLogin($company);
        }

        // Verify TOTP code
        $secret = decrypt($company->two_factor_secret);
        $valid = $this->google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid verification code.']);
        }

        Auth::guard('company')->login($company);
        $request->session()->regenerate();
        session()->forget(['2fa_required', '2fa_company_id']);

        return $this->redirectAfterLogin($company);
    }

    /**
     * Generate recovery codes
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }

    /**
     * Redirect after successful login
     */
    protected function redirectAfterLogin(Company $company)
    {
        switch ($company->role ?? 'client') {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'developer':
                return redirect()->intended(route('api.documentation'));
            case 'client':
            default:
                return redirect()->intended(route('client.dashboard'));
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OtpToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    /**
     * Send OTP for login
     */
    public function sendLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $company = Company::where('email', $request->email)->first();

        if (!$company) {
            return back()->withErrors(['email' => 'Invalid email address.']);
        }

        // Generate 6-digit OTP
        $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old OTPs for this email
        OtpToken::where('email', $company->email)
            ->where('type', 'login')
            ->delete();

        // Create new OTP
        OtpToken::create([
            'email' => $company->email,
            'otp' => $otp,
            'type' => 'login',
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::send('emails.login-otp', [
                'company' => $company,
                'otp' => $otp,
            ], function ($message) use ($company) {
                $message->to($company->email, $company->name)
                    ->subject('Login OTP - Phishing Simulation Platform');
            });

            return back()->with('status', 'OTP has been sent to your email.');
        } catch (\Exception $e) {
            Log::error('Failed to send login OTP', [
                'error' => $e->getMessage(),
                'email' => $company->email,
            ]);

            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
    }

    /**
     * Verify OTP for login
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $otpToken = OtpToken::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('type', 'login')
            ->where('used', false)
            ->first();

        if (!$otpToken || !$otpToken->isValid()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $company = Company::where('email', $request->email)->firstOrFail();

        // Mark OTP as used
        $otpToken->markAsUsed();

        // Login the company
        Auth::guard('company')->login($company);

        $request->session()->regenerate();

        // Redirect based on role
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

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:login,password_reset',
        ]);

        return $this->sendLoginOtp($request);
    }
}

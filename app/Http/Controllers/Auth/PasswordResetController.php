<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OtpToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link or OTP
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'method' => 'required|in:email,otp',
        ]);

        $company = Company::where('email', $request->email)->first();

        if (!$company) {
            // Don't reveal if email exists for security
            return back()->with('status', 'If that email exists, we have sent password reset instructions.');
        }

        if ($request->method === 'otp') {
            return $this->sendOtp($company);
        } else {
            return $this->sendResetEmail($company);
        }
    }

    /**
     * Send password reset email with token
     */
    protected function sendResetEmail(Company $company)
    {
        $token = Str::random(64);

        DB::table('company_password_reset_tokens')->updateOrInsert(
            ['email' => $company->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        try {
            Mail::send('emails.password-reset', [
                'company' => $company,
                'resetUrl' => route('password.reset', ['token' => $token, 'email' => $company->email]),
            ], function ($message) use ($company) {
                $message->to($company->email, $company->name)
                    ->subject('Reset Your Password - Phishing Simulation Platform');
            });

            return back()->with('status', 'Password reset link has been sent to your email.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'error' => $e->getMessage(),
                'email' => $company->email,
            ]);

            return back()->withErrors(['email' => 'Failed to send reset email. Please try again.']);
        }
    }

    /**
     * Send OTP for password reset
     */
    protected function sendOtp(Company $company)
    {
        // Generate 6-digit OTP
        $otp = str_pad((string) rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old OTPs for this email
        OtpToken::where('email', $company->email)
            ->where('type', 'password_reset')
            ->delete();

        // Create new OTP
        OtpToken::create([
            'email' => $company->email,
            'otp' => $otp,
            'type' => 'password_reset',
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::send('emails.password-reset-otp', [
                'company' => $company,
                'otp' => $otp,
            ], function ($message) use ($company) {
                $message->to($company->email, $company->name)
                    ->subject('Password Reset OTP - Phishing Simulation Platform');
            });

            return redirect()->route('password.reset.otp')
                ->with('email', $company->email)
                ->with('status', 'OTP has been sent to your email.');
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email', [
                'error' => $e->getMessage(),
                'email' => $company->email,
            ]);

            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
    }

    /**
     * Show reset password form (token method)
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Show reset password form (OTP method)
     */
    public function showResetFormOtp()
    {
        return view('auth.reset-password-otp');
    }

    /**
     * Reset password using token
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetToken = DB::table('company_password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetToken || !Hash::check($request->token, $resetToken->token)) {
            return back()->withErrors(['email' => 'Invalid or expired reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetToken->created_at) > 60) {
            DB::table('company_password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        $company = Company::where('email', $request->email)->firstOrFail();
        $company->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used token
        DB::table('company_password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password has been reset successfully. Please login.');
    }

    /**
     * Reset password using OTP
     */
    public function resetWithOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpToken = OtpToken::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('type', 'password_reset')
            ->where('used', false)
            ->first();

        if (!$otpToken || !$otpToken->isValid()) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        $company = Company::where('email', $request->email)->firstOrFail();
        $company->update([
            'password' => Hash::make($request->password),
        ]);

        // Mark OTP as used
        $otpToken->markAsUsed();

        return redirect()->route('login')->with('status', 'Password has been reset successfully. Please login.');
    }
}

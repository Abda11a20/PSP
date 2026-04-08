<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        if (Auth::guard('company')->check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('company')->attempt($credentials, $request->remember)) {
            $company = Auth::guard('company')->user();
            
            // Check if 2FA is enabled
            if ($company->two_factor_enabled) {
                // Store company ID in session and require 2FA verification
                session([
                    '2fa_required' => true,
                    '2fa_company_id' => $company->id,
                ]);
                
                // Logout temporarily until 2FA is verified
                Auth::guard('company')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('2fa.verify');
            }
            
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

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        if (Auth::guard('company')->check()) {
            return redirect()->route('home');
        }

        $plans = \App\Models\Plan::all();

        return view('auth.register', compact('plans'));
    }

    /**
     * Handle registration request.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'password' => 'required|string|min:8|confirmed',
            'plan_id' => 'required|exists:plans,id',
        ]);

        // Get the selected plan
        $selectedPlan = \App\Models\Plan::findOrFail($request->plan_id);
        
        // Check if it's a Custom plan
        $isCustomPlan = stripos($selectedPlan->name, 'custom') !== false;
        
        // Determine initial plan: if free plan selected, use it; otherwise use free plan as default
        $initialPlanId = $selectedPlan->id;
        
        // Get price value safely (handle potential null or invalid values)
        $planPrice = $selectedPlan->getPriceFloat();
        
        // For Custom plan, always use Free Plan and redirect to support
        if ($isCustomPlan) {
            $freePlan = \App\Models\Plan::where('price', 0)->first();
            if (!$freePlan) {
                $freePlan = \App\Models\Plan::create([
                    'name' => 'Free Plan',
                    'price' => 0.00,
                    'employee_limit' => 10,
                ]);
            }
            $initialPlanId = $freePlan->id;
        } elseif ($planPrice > 0) {
            // For paid plans, get or create free plan as default (will be updated after payment)
            $freePlan = \App\Models\Plan::where('price', 0)->first();
            if (!$freePlan) {
                $freePlan = \App\Models\Plan::create([
                    'name' => 'Free Plan',
                    'price' => 0.00,
                    'employee_limit' => 10,
                ]);
            }
            $initialPlanId = $freePlan->id;
        }

        // Create company with initial plan
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_id' => $initialPlanId,
            'role' => 'client', // Default role
        ]);

        Auth::guard('company')->login($company);

        // If Custom plan selected, redirect to contact support
        if ($isCustomPlan) {
            return redirect()->route('client.contact-support')
                ->with('info', 'Thank you for your interest in a Custom Plan! Please contact our support team to discuss your requirements and we\'ll create a tailored solution for you.');
        }

        // If selected plan is free, redirect to dashboard
        if ($planPrice == 0) {
            return redirect()->route('client.dashboard');
        }

        // For paid plans, initialize payment and redirect to checkout
        try {
            $paymentService = app(\App\Services\PaymentService::class);
            $result = $paymentService->initializePayment($company->id, $request->plan_id);
            
            // Redirect to checkout page
            return redirect($result['checkout_url']);
        } catch (\Exception $e) {
            // If payment initialization fails, redirect to dashboard with error
            return redirect()->route('client.dashboard')
                ->with('error', 'Account created but payment initialization failed. Please contact support.');
        }
    }

    /**
     * Log the user out.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

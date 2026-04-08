<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Models\Company;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new company",
     *     description="Create a new company account with authentication credentials",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Acme Corporation"),
     *             @OA\Property(property="email", type="string", format="email", example="admin@acme.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Company registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Company registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                     @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                     @OA\Property(property="plan_id", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password confirmation does not match."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error - No default plan available",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No default plan available. Please contact support.")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        // Get the default plan, or create one if none exists
        $defaultPlan = Plan::first();
        
        if (!$defaultPlan) {
            // Automatically create a default free plan if none exists
            $defaultPlan = Plan::create([
                'name' => 'Free Plan',
                'price' => 0.00,
                'employee_limit' => 10,
            ]);
        }

        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_id' => $defaultPlan->id,
        ]);

        $token = JWTAuth::fromUser($company);

        return response()->json([
            'message' => 'Company registered successfully',
            'data' => new AuthResource($company, $token),
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login company",
     *     description="Authenticate company and return access token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@acme.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                     @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                     @OA\Property(property="plan_id", type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abc123...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The provided credentials are incorrect."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $company = Auth::guard('api')->user();

        return response()->json([
            'message' => 'Login successful',
            'data' => new AuthResource($company, $token),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout company",
     *     description="Logout the authenticated company and revoke the current access token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh access token",
     *     description="Refresh the current access token by revoking the old one and creating a new one",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                     @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                     @OA\Property(property="plan_id", type="integer", example=1),
     *                     @OA\Property(property="plan", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Free"),
     *                         @OA\Property(property="price", type="number", format="float", example=0.00),
     *                         @OA\Property(property="employee_limit", type="integer", example=10)
     *                     )
     *                 ),
     *                 @OA\Property(property="token", type="string", example="2|new123token456def789ghi012jkl345mno678pqr901stu234")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request)
    {
        try {
            $token = JWTAuth::refresh(JWTAuth::getToken());
            /** @var Company $company */
            $company = Auth::guard('api')->user();

            if (!$company) {
                return response()->json([
                    'message' => 'Could not refresh token',
                ], 401);
            }

            return response()->json([
                'message' => 'Token refreshed successfully',
                'data' => new AuthResource($company, $token),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Could not refresh token',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get current company profile",
     *     description="Get the profile information of the currently authenticated company",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Company profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Company profile retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                 @OA\Property(property="email", type="string", example="admin@acme.com"),
     *                 @OA\Property(property="plan_id", type="integer", example=1),
     *                 @OA\Property(property="plan", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Free"),
     *                     @OA\Property(property="price", type="number", format="float", example=0.00),
     *                     @OA\Property(property="employee_limit", type="integer", example=10)
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-06T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-06T10:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function me(Request $request)
    {
        /** @var Company $company */
        $company = Auth::guard('api')->user();
        
        if (!$company) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }
        
        $company->load('plan');

        return response()->json([
            'message' => 'Company profile retrieved successfully',
            'data' => $company,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/change-password",
     *     summary="Change company password",
     *     description="Change the password for the currently authenticated company",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password","new_password","new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid current password",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="current_password", type="array", @OA\Items(type="string", example="The current password is incorrect."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="new_password", type="array", @OA\Items(type="string", example="The new password confirmation does not match."))
     *             )
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string',
        ]);

        /** @var Company $company */
        $company = Auth::guard('api')->user();

        if (!$company) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if (!Hash::check($request->current_password, $company->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $company->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ]);
    }
}

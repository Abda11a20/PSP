<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CheckoutRequest;
use App\Http\Requests\Payment\ConfirmPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Get(
     *     path="/api/plans",
     *     summary="List available subscription plans",
     *     description="Get all available subscription plans with pricing and features",
     *     tags={"Payment"},
     *     @OA\Response(
     *         response=200,
     *         description="Plans retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plans retrieved successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Basic"),
     *                     @OA\Property(property="price", type="number", format="float", example=10.00),
     *                     @OA\Property(property="employee_limit", type="integer", example=50),
     *                     @OA\Property(property="features", type="array", @OA\Items(type="string"))
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function plans(Request $request)
    {
        $plans = Plan::where('price', '>', 0)->get(); // Exclude free plan
        
        return response()->json([
            'message' => 'Plans retrieved successfully',
            'data' => PlanResource::collection($plans)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/payment/checkout",
     *     summary="Initialize payment checkout",
     *     description="Initialize a payment checkout process for a subscription plan",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plan_id"},
     *             @OA\Property(property="plan_id", type="integer", example=2, description="ID of the subscription plan")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment initialized successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment initialized successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef"),
     *                 @OA\Property(property="checkout_url", type="string", example="https://payment-gateway.com/checkout/txn_123456789abcdef"),
     *                 @OA\Property(property="amount", type="number", format="float", example=10.00),
     *                 @OA\Property(property="plan", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Basic"),
     *                     @OA\Property(property="price", type="number", format="float", example=10.00),
     *                     @OA\Property(property="employee_limit", type="integer", example=50)
     *                 ),
     *                 @OA\Property(property="expires_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function checkout(CheckoutRequest $request)
    {
        $company = $request->user();
        
        $selectedPlan = Plan::findOrFail($request->plan_id);
        $currentPlan = $company->plan;
        
        // Allow checkout if:
        // 1. Selected plan price is higher than current plan (upgrade)
        // 2. Selected plan price is same as current but different plan (plan change)
        // 3. Current plan is free and selected plan is paid (initial purchase)
        $selectedPrice = $selectedPlan->getPriceFloat();
        $currentPrice = $currentPlan->getPriceFloat();
        if ($selectedPrice < $currentPrice) {
            return response()->json([
                'message' => 'You can only upgrade to a higher or equal priced plan'
            ], 400);
        }

        // If trying to checkout the same plan, return error
        if ($selectedPlan->id === $currentPlan->id) {
            return response()->json([
                'message' => 'You are already on this plan'
            ], 400);
        }

        try {
            $result = $this->paymentService->initializePayment($company->id, $request->plan_id);
            
            return response()->json([
                'message' => 'Checkout initialized successfully',
                'data' => [
                    'payment_id' => $result['payment_id'],
                    'transaction_id' => $result['transaction_id'],
                    'checkout_url' => $result['checkout_url'],
                    'amount' => $result['amount'],
                    'plan' => new PlanResource($result['plan']),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to initialize payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/payment/confirm",
     *     summary="Confirm payment",
     *     description="Confirm a payment transaction and update company subscription plan",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"transaction_id"},
     *             @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef", description="Transaction ID from payment gateway")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment confirmed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment confirmed successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="payment", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef"),
     *                     @OA\Property(property="status", type="string", example="completed"),
     *                     @OA\Property(property="amount", type="number", format="float", example=10.00),
     *                     @OA\Property(property="plan", type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Basic"),
     *                         @OA\Property(property="employee_limit", type="integer", example=50)
     *                     )
     *                 ),
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Acme Corporation"),
     *                     @OA\Property(property="plan_id", type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Payment confirmation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment confirmation failed"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="payment", type="object",
     *                     @OA\Property(property="status", type="string", example="failed")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="transaction_id", type="array", @OA\Items(type="string", example="The transaction id field is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to confirm payment"),
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function confirm(ConfirmPaymentRequest $request)
    {
        try {
            $result = $this->paymentService->confirmPayment($request->transaction_id);
            
            if ($result['success']) {
                return response()->json([
                    'message' => $result['message'],
                    'data' => [
                        'payment' => new PaymentResource($result['payment']),
                        'company' => $result['company'],
                    ]
                ]);
            } else {
                return response()->json([
                    'message' => $result['message'],
                    'data' => [
                        'payment' => new PaymentResource($result['payment']),
                    ]
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to confirm payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="Get payment history",
     *     description="Get payment history for the authenticated company",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of payments per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter payments by status",
     *         @OA\Schema(type="string", enum={"pending","completed","failed","cancelled"}, example="completed")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment history retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment history retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="payments", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef"),
     *                         @OA\Property(property="status", type="string", example="completed"),
     *                         @OA\Property(property="amount", type="number", format="float", example=10.00),
     *                         @OA\Property(property="plan", type="object",
     *                             @OA\Property(property="id", type="integer", example=2),
     *                             @OA\Property(property="name", type="string", example="Basic"),
     *                             @OA\Property(property="price", type="number", format="float", example=10.00)
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 ),
     *                 @OA\Property(property="total_payments", type="integer", example=5),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=150.00)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function index(Request $request)
    {
        $company = $request->user();
        $result = $this->paymentService->getPaymentHistory($company->id);
        
        return response()->json([
            'message' => 'Payment history retrieved successfully',
            'data' => [
                'payments' => PaymentResource::collection($result['payments']),
                'total_payments' => $result['total_payments'],
                'total_amount' => $result['total_amount'],
            ]
        ]);
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        // This method is kept for backward compatibility
        // Use the checkout method instead
        return $this->checkout($request);
    }

    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     summary="Get payment details",
     *     description="Get detailed information about a specific payment",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment details retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="company_id", type="integer", example=1),
     *                 @OA\Property(property="plan_id", type="integer", example=2),
     *                 @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef"),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="amount", type="number", format="float", example=10.00),
     *                 @OA\Property(property="plan", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="Basic"),
     *                     @OA\Property(property="price", type="number", format="float", example=10.00),
     *                     @OA\Property(property="employee_limit", type="integer", example=50)
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Access denied",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function show(string $id)
    {
        $company = request()->user();
        
        $payment = $company->payments()
            ->with('plan')
            ->findOrFail($id);

        return response()->json([
            'message' => 'Payment details retrieved successfully',
            'data' => new PaymentResource($payment)
        ]);
    }

    /**
     * Process a payment
     */
    public function process(string $id)
    {
        // This method is kept for backward compatibility
        // Use the confirm method instead
        return response()->json([
            'message' => 'Use POST /api/payment/confirm with transaction_id instead'
        ], 400);
    }

    /**
     * @OA\Get(
     *     path="/api/payment/status/{transactionId}",
     *     summary="Get payment status",
     *     description="Get the current status of a payment transaction",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="transactionId",
     *         in="path",
     *         required=true,
     *         description="Transaction ID",
     *         @OA\Schema(type="string", example="txn_123456789abcdef")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment status retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef"),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="amount", type="number", format="float", example=10.00),
     *                 @OA\Property(property="plan_name", type="string", example="Basic"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="completed_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found"),
     *             @OA\Property(property="error", type="string", example="Transaction not found")
     *         )
     *     )
     * )
     */
    public function status(Request $request, string $transactionId)
    {
        try {
            $result = $this->paymentService->getPaymentStatus($transactionId);
            
            return response()->json([
                'message' => 'Payment status retrieved successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Payment not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/payment/cancel/{transactionId}",
     *     summary="Cancel payment",
     *     description="Cancel a pending payment transaction",
     *     tags={"Payment"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="transactionId",
     *         in="path",
     *         required=true,
     *         description="Transaction ID",
     *         @OA\Schema(type="string", example="txn_123456789abcdef")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment cancelled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment cancelled successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="transaction_id", type="string", example="txn_123456789abcdef"),
     *                 @OA\Property(property="status", type="string", example="cancelled"),
     *                 @OA\Property(property="amount", type="number", format="float", example=10.00),
     *                 @OA\Property(property="cancelled_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(ref="#/components/schemas/Error")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to cancel payment"),
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function cancel(Request $request, string $transactionId)
    {
        try {
            $result = $this->paymentService->cancelPayment($transactionId);
            
            return response()->json([
                'message' => $result['message'],
                'data' => new PaymentResource($result['payment'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

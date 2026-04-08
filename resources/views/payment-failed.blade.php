<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Phishing Simulation Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-red-100">
                    <i class="fas fa-times text-red-600 text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Payment Failed
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    We couldn't process your payment
                </p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <!-- Error Message -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Unsuccessful</h3>
                    <p class="text-sm text-gray-600">
                        Unfortunately, we couldn't process your payment for the 
                        <strong>{{ $payment->plan->name }}</strong> plan.
                    </p>
                </div>

                <!-- Payment Details -->
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Payment Details</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Plan:</span>
                            <span class="font-medium">{{ $payment->plan->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-medium">${{ number_format($payment->amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transaction ID:</span>
                            <span class="font-mono text-xs">{{ $payment->transaction_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium text-red-600">{{ ucfirst($payment->status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Common Reasons -->
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Common Reasons for Payment Failure:</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-credit-card text-gray-400 mr-2 mt-0.5"></i>
                            Insufficient funds or expired card
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-shield-alt text-gray-400 mr-2 mt-0.5"></i>
                            Bank security restrictions
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-wifi text-gray-400 mr-2 mt-0.5"></i>
                            Network connectivity issues
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-user-shield text-gray-400 mr-2 mt-0.5"></i>
                            Incorrect card details
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('checkout', $payment->transaction_id) }}" 
                       class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-redo mr-2"></i>
                        Try Payment Again
                    </a>
                    
                    <a href="{{ route('client.dashboard') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>

                <!-- Support Notice -->
                <div class="mt-6 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex">
                        <i class="fas fa-question-circle text-yellow-500 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-xs text-yellow-800">
                                <strong>Need Help?</strong> If you continue to experience issues, 
                                please contact our support team for assistance.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    <a href="mailto:support@phishingsim.com" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-envelope mr-1"></i>
                        Contact Support
                    </a>
                    or call us at 
                    <a href="tel:+1-800-555-0123" class="text-blue-600 hover:text-blue-500">
                        +1-800-555-0123
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

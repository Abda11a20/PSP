<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Phishing Simulation Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-green-100">
                    <i class="fas fa-check text-green-600 text-2xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Payment Successful!
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Your subscription has been upgraded successfully
                </p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <!-- Success Message -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Confirmed</h3>
                    <p class="text-sm text-gray-600">
                        Thank you for your payment. Your account has been upgraded to the 
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
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $payment->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Plan Features -->
                @if($payment->plan->features)
                <div class="border border-gray-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Your New Plan Includes:</h4>
                    <ul class="space-y-2">
                        @foreach(json_decode($payment->plan->features, true) as $feature)
                            <li class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="{{ route('client.dashboard') }}" 
                       class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                    
                    <a href="{{ route('client.campaigns.create') }}" 
                       class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Campaign
                    </a>
                </div>

                <!-- Receipt Notice -->
                <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-xs text-blue-800">
                                A receipt has been sent to your email address. 
                                You can also download it from your dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    Need help? <a href="mailto:support@phishingsim.com" class="text-blue-600 hover:text-blue-500">Contact Support</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

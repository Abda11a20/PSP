<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Phishing Simulation Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                    <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Complete Your Payment
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Upgrade to {{ $planName }} Plan
                </p>
            </div>

            <div class="bg-white shadow-lg rounded-lg p-6">
                <!-- Plan Details -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $plan->name }} Plan</h3>
                    <div class="mt-2 space-y-1">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Up to {{ $plan->employee_limit }} employees
                        </p>
                        @if($plan->features)
                            @foreach(json_decode($plan->features, true) as $feature)
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    {{ $feature }}
                                </p>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Subtotal:</span>
                        <span class="text-sm text-gray-900">${{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-sm font-medium text-gray-700">Tax:</span>
                        <span class="text-sm text-gray-900">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                        <span class="text-lg font-bold text-gray-900">Total:</span>
                        <span class="text-lg font-bold text-blue-600">${{ number_format($amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('checkout.process', $transactionId) }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Card Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                        <div class="relative">
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="1234 5678 9012 3456"
                                   maxlength="19"
                                   pattern="[0-9\s]{13,19}"
                                   required>
                            <i class="fas fa-credit-card absolute right-3 top-3 text-gray-400"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="MM/YY"
                                   maxlength="5"
                                   pattern="[0-9]{2}/[0-9]{2}"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="123"
                                   maxlength="4"
                                   pattern="[0-9]{3,4}"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="John Doe"
                               required>
                    </div>

                    <!-- Payment Buttons -->
                    <div class="space-y-3">
                        <button type="submit" 
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <i class="fas fa-lock mr-2"></i>
                            Pay ${{ number_format($amount, 2) }}
                        </button>
                        
                        <form action="{{ route('payment.cancel', $transactionId) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                <i class="fas fa-times mr-2"></i>
                                Cancel Payment
                            </button>
                        </form>
                    </div>
                </form>

                <!-- Security Notice -->
                <div class="mt-6 p-3 bg-green-50 border border-green-200 rounded-md">
                    <div class="flex">
                        <i class="fas fa-shield-alt text-green-500 mt-0.5 mr-2"></i>
                        <div>
                            <p class="text-xs text-green-800">
                                <strong>Secure Payment:</strong> Your payment information is encrypted and secure. 
                                This is a simulation for testing purposes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Info -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    Transaction ID: {{ $transactionId }}
                </p>
            </div>
        </div>
    </div>

    <script>
        // Format card number input
        document.querySelector('input[placeholder="1234 5678 9012 3456"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Format expiry date input
        document.querySelector('input[placeholder="MM/YY"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Format CVV input
        document.querySelector('input[placeholder="123"]').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>

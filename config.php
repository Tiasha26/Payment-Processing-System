<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize session data if not exists
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [];
}

if (!isset($_SESSION['transactions'])) {
    $_SESSION['transactions'] = [];
}

// Payment method configuration
$payment_method_config = [
    'credit_card' => [
        'transaction_fee' => 1.00,
        'processing_time' => 5,
        'min_amount' => 10,
        'max_amount' => 100000
    ],
    'paypal' => [
        'transaction_fee' => 2.00,
        'processing_time' => 10,
        'min_amount' => 10,
        'max_amount' => 100000
    ],
    'cryptocurrency' => [
        'transaction_fee' => 0.00,
        'processing_time' => 0,
        'min_amount' => 10,
        'max_amount' => 100000
    ]
];

// Function to get or create user
function getOrCreateUser($name, $initial_balance) {
    if (isset($_SESSION['users'][$name])) {
        return $_SESSION['users'][$name];
    } else {
        $_SESSION['users'][$name] = [
            'name' => $name,
            'balance' => $initial_balance
        ];
        return $_SESSION['users'][$name];
    }
}

// Function to process refund
function processRefund($transaction_id) {
    if (isset($_SESSION['transactions'][$transaction_id])) {
        $transaction = $_SESSION['transactions'][$transaction_id];
        
        // Update transaction status
        $_SESSION['transactions'][$transaction_id]['status'] = 'refunded';
        
        // Update user balance
        $user_name = $transaction['name'];
        $_SESSION['users'][$user_name]['balance'] += $transaction['amount'];
        
        return true;
    }
    return false;
}
?> 
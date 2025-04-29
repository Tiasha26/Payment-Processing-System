<?php
session_start();
require_once 'config.php';

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $initial_balance = floatval($_POST['balance'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);
    $payment_method = $_POST['payment_method'] ?? '';

    // Validate amount
    if ($amount > 100000) {
        $_SESSION['error'] = "Error: Amount must be less than R100,000.";
        header("Location: index.php");
        exit();
    }

    // Validate payment method
    if (!isset($payment_method_config[$payment_method])) {
        $_SESSION['error'] = "Error: Invalid payment method selected.";
        header("Location: index.php");
        exit();
    }

    // Get payment method configuration
    $config = $payment_method_config[$payment_method];
    $total_amount = $amount + $config['transaction_fee'];

    // Get or create user
    $user = getOrCreateUser($name, $initial_balance);

    // Validate balance
    if ($user['balance'] < $total_amount) {
        $_SESSION['error'] = "Error: Insufficient balance.";
        header("Location: index.php");
        exit();
    }

    // Create transaction
    $transaction_id = count($_SESSION['transactions']);
    $transaction = [
        'id' => $transaction_id,
        'name' => $name,
        'amount' => $amount,
        'payment_method' => $payment_method,
        'transaction_fee' => $config['transaction_fee'],
        'status' => 'completed',
        'created_at' => date('Y-m-d H:i:s'),
        'new_balance' => $_SESSION['users'][$name]['balance'] - $total_amount
    ];

    // Update user balance
    $_SESSION['users'][$name]['balance'] -= $total_amount;

    // Store transaction
    $_SESSION['transactions'][$transaction_id] = $transaction;

    // Store transaction details in session for confirmation page
    $_SESSION['transaction'] = $transaction;

    header("Location: payment_confirmation.php");
    exit();
}

// If not a POST request, redirect to index
header("Location: index.php");
exit();
?>
<?php
session_start();
require_once 'config.php';

// Check if a transaction ID was provided
if (!isset($_GET['transaction_id']) || !isset($_SESSION['transactions'][$_GET['transaction_id']])) {
    $_SESSION['error'] = "Invalid transaction ID.";
    header("Location: refund.php");
    exit();
}

$transaction_id = $_GET['transaction_id'];
$transaction = $_SESSION['transactions'][$transaction_id];
$user_name = $transaction['name'];
$user = $_SESSION['users'][$user_name];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Refund Details</title>
</head>
<body class="p-2">
    <div class="container">
        <h1 class="text-center mb-4">Refund Details</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-3">
                    <h3 class="text-center mb-3">Refund Information</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Name:</th>
                                <td><?php echo htmlspecialchars($user_name); ?></td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])); ?></td>
                            </tr>
                            <tr>
                                <th>Refunded Amount:</th>
                                <td>R<?php echo number_format($transaction['amount'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Transaction Fee:</th>
                                <td>R<?php echo number_format($transaction['transaction_fee'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Transaction Date:</th>
                                <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <th>Refund Date:</th>
                                <td><?php echo date('Y-m-d H:i'); ?></td>
                            </tr>
                            <tr>
                                <th>Updated Balance:</th>
                                <td>R<?php echo number_format($user['balance'], 2); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="refund.php" class="btn btn-secondary">Back to Refund System</a>
            <a href="index.php" class="btn btn-primary">Go to Payment System</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
<?php
session_start();
require_once 'config.php';

// Check if there's a transaction to display
if (!isset($_SESSION['transaction'])) {
    header("Location: index.php");
    exit();
}

$transaction = $_SESSION['transaction'];

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Payment Confirmation</title>
</head>
<body class="p-2">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-3">
                    <h2 class="text-center mb-4">Payment Confirmation</h2>
                    
                    <div class="alert alert-success">
                        <h4>Payment Successful!</h4>
                        <p>Transaction ID: <?php echo $transaction['id']; ?></p>
                    </div>

                    <div class="mb-4">
                        <h5>Transaction Details:</h5>
                        <table class="table">
                            <tr>
                                <th>Name:</th>
                                <td><?php echo htmlspecialchars($transaction['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Amount:</th>
                                <td>R<?php echo number_format($transaction['amount'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])); ?></td>
                            </tr>
                            <tr>
                                <th>Transaction Fee:</th>
                                <td>R<?php echo number_format($transaction['transaction_fee'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>New Balance:</th>
                                <td>R<?php echo number_format($transaction['new_balance'], 2); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="text-center">
                            <a href="index.php" class="btn btn-primary">Make Another Payment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
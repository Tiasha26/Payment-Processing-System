<?php
session_start();
require_once 'config.php';

$transactions = [];
$name = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
        // Get all transactions for this user
        foreach ($_SESSION['transactions'] as $transaction) {
            if ($transaction['name'] === $name && $transaction['status'] === 'completed') {
                $transactions[] = $transaction;
            }
        }
        // Sort by date, most recent first
        usort($transactions, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
    }
    
    // Handle refund request
    if (isset($_POST['refund_transaction_id'])) {
        $transaction_id = intval($_POST['refund_transaction_id']);
        
        // Validate transaction exists and is in correct state
        if (!isset($_SESSION['transactions'][$transaction_id])) {
            $_SESSION['error'] = "Invalid transaction ID.";
            header("Location: refund.php");
            exit();
        }
        
        $transaction = $_SESSION['transactions'][$transaction_id];
        if ($transaction['status'] !== 'completed') {
            $_SESSION['error'] = "This transaction cannot be refunded.";
            header("Location: refund.php");
            exit();
        }
        
        if (processRefund($transaction_id)) {
            // Redirect to refund details page
            header("Location: refund_details.php?transaction_id=" . $transaction_id);
            exit();
        } else {
            $_SESSION['error'] = "Failed to process refund. Please try again.";
            header("Location: refund.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Refund System</title>
</head>
<body class="p-2">
    <div class="container">
        <h1 class="text-center mb-4">Refund System</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-3">
                    <form method="post">
                        <div class="form-group mb-3">
                            <label for="name">Enter your name to view refundable transactions:</label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?php echo htmlspecialchars($name); ?>">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">View Transactions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if (!empty($transactions)): ?>
        <div class="row justify-content-center mt-4">
            <div class="col-md-8">
                <div class="card p-3">
                    <h3 class="text-center mb-3">Refundable Transactions</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Fee</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                                <td>R<?php echo number_format($transaction['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])); ?></td>
                                <td>R<?php echo number_format($transaction['transaction_fee'], 2); ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Are you sure you want to refund this transaction?');">
                                        <input type="hidden" name="refund_transaction_id" value="<?php echo $transaction['id']; ?>">
                                        <button type="submit" class="btn btn-warning">Refund</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary">Back to Payment System</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
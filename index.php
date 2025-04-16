<?php
session_start();
require_once 'config.php';

// Get user's transaction history if name is provided
$transactions = [];
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    foreach ($_SESSION['transactions'] as $transaction) {
        if ($transaction['name'] === $name) {
            $transactions[] = $transaction;
        }
    }
    // Sort by date, most recent first
    usort($transactions, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    // Get only the last 5 transactions
    $transactions = array_slice($transactions, 0, 5);
}
?>

<!DOCTYPE html>
<html>
<head>  
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Payment Processing System</title>
</head>
<body class="p-2">
    <div class="container">
        <h1 class="text-center mb-4">Payment Processing System</h1>
        
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
                    <form action="payment.php" method="post">
                        <div class="form-group mb-3">
                            <label for="name">Name:</label>
                            <input type="text" name="name" class="form-control" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"> 
                        </div>
                        <div class="form-group mb-3">
                            <label for="balance">Balance:</label>
                            <input type="number" name="balance" class="form-control" required 
                                   value="<?php echo isset($_POST['balance']) ? htmlspecialchars($_POST['balance']) : ''; ?>"> 
                        </div>
                        <div class="form-group mb-3">
                            <label for="amount">Amount:</label>
                            <input type="number" id="amount" name="amount" class="form-control" required max="100000"> 
                        </div>
                        <div class="form-group mb-3">
                            <label for="payment_method">Payment Method:</label>
                            <select name="payment_method" class="form-control" required>
                                <option value="">Select payment method</option>
                                <option value="credit_card">Credit Card (1% fee)</option>
                                <option value="paypal">PayPal (2% fee)</option>
                                <option value="cryptocurrency">Cryptocurrency (No fee)</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" onclick="return fraudcheck()">Pay now</button>
                            <a href="refund.php" class="btn btn-warning">Request Refund</a>
                            <button type="button" class="btn btn-info" onclick="displayTransactions()">Display Transactions</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="transactionsTable" class="row justify-content-center mt-4" style="display: none;">
            <div class="col-md-8">
                <div class="card p-3">
                    <h3 class="text-center mb-3">All Transactions</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Fee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['transactions'] as $transaction): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['name']); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($transaction['created_at'])); ?></td>
                                <td>R<?php echo number_format($transaction['amount'], 2); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])); ?></td>
                                <td>R<?php echo number_format($transaction['transaction_fee'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $transaction['status'] === 'completed' ? 'success' : 'warning'; ?>">
                                        <?php echo ucfirst($transaction['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fraudcheck() {
            const amt = document.getElementById('amount').value;
            if (amt > 100000) {
                alert('No Payments above R100,000 are allowed.');
                return false;
            }
            return true;
        }

        function displayTransactions() {
            const table = document.getElementById('transactionsTable');
            if (table.style.display === 'none') {
                table.style.display = 'block';
            } else {
                table.style.display = 'none';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
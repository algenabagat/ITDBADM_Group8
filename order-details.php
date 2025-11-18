<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: profile.php");
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id  = (int)$_SESSION['user_id'];

$conn = getDBConnection($host, $user, $password, $database, $port);

// Fetch order
$sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    $conn->close();
    header("Location: profile.php?error=" . urlencode("Order not found"));
    exit;
}

// Fetch order items
$sql = "SELECT oi.*, p.product_name, p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order #<?php echo $order_id; ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/icon.png">

</head>

<body class="bg-light">

<div class="container py-5">

    <h2 class="mb-4">Order #<?php echo $order_id; ?></h2>

    <div class="card p-4 mb-4">
        <h4>Order Summary</h4>
        <p>Status: <strong><?php echo $order['status']; ?></strong></p>
        <p>Total Amount: <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></p>
        <p><?php echo date("F d, Y h:i A", strtotime($order['order_date'])); ?></p>

    </div>

    <div class="card p-4">
        <h4>Items</h4>
        <table class="table">
            <thead>
                <tr>
                    <th style="width:80px;">Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?php echo $item['image_url']; ?>" width="60">
                    </td>
                    <td><?php echo $item['product_name']; ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <a href="profile.php" class="btn btn-secondary mt-4">Back to Orders</a>

</div>

</body>
</html>

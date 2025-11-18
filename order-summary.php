<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['last_order'])) {
    header("Location: index.php");
    exit;
}

$last = $_SESSION['last_order'];
$order_id = (int)$last['order_id'];

$conn = getDBConnection($host, $user, $password, $database, $port);
$orderItems = [];

if ($conn) {
    $stmt = $conn->prepare("SELECT oi.product_id, oi.quantity, oi.price, p.product_name 
                            FROM order_items oi
                            JOIN products p ON oi.product_id = p.product_id
                            WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $orderItems[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Zeit - Order Summary</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="icon" type="image/x-icon" href="img/icon.png">

</head>
<body>
<div class="container-fluid">
  <?php include 'header-navbar.php'; ?>

  <div class="container py-5">
    <div class="card">
      <div class="card-header">
        <h3>Order Placed Successfully</h3>
      </div>
      <div class="card-body">
        <p>Thank you, <strong><?php echo htmlspecialchars($last['full_name']); ?></strong>!</p>
        <p>Your order number is <strong>#<?php echo $order_id; ?></strong>.</p>

        <h5 class="mt-4">Order Details</h5>
        <div class="mb-2"><strong>Shipping Address:</strong> <?php echo nl2br(htmlspecialchars($last['address'])); ?></div>
        <div class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($last['phone']); ?></div>
        <div class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($last['email']); ?></div>
        <div class="mb-2"><strong>Payment Method:</strong> <?php echo htmlspecialchars($last['payment_method']); ?></div>

        <h5 class="mt-4">Items</h5>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            
            <tbody>
            <?php
            $grand = 0;

            // currency session values from checkout
            $currency = $last['currency'] ?? 'PHP';
            $symbol   = $last['symbol'] ?? '₱';
            $rate     = $last['rates'][$currency] ?? 1;
            ?>

            <?php foreach ($orderItems as $item): 
                  $sub = $item['price'] * $item['quantity'];
                  $grand += $sub;
            ?>
              
              <tr>
              <td><?php echo htmlspecialchars($item['product_name']); ?></td>
              <td><?php echo (int)$item['quantity']; ?></td>

              <!-- Converted price -->
              <td>
                  <?php echo $symbol . number_format($item['price'] * $rate, 2); ?>
              </td>

              <!-- Converted subtotal -->
              <td>
                  <?php echo $symbol . number_format(($item['price'] * $item['quantity']) * $rate, 2); ?>
              </td>
              </tr>

              <?php $grand += $item['price'] * $item['quantity']; ?>
              
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end">
          <h4>Total: <?php echo $symbol . number_format($last['converted_total'], 2); ?></h4>
        </div>

        <a href="index.php" class="btn btn-dark mt-4">Back to Home</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<?php if ($conn) $conn->close(); ?>

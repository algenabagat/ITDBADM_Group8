<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please log in to view your cart."));
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$conn = getDBConnection($host, $user, $password, $database, $port);

$cartItems = [];
$total = 0.00;

if ($conn) {
    $sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.price, p.image_url, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $cartItems[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Zeit - Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<div class="container-fluid">
  <?php include 'header-navbar.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4">Your Cart</h2>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
      <p>Your cart is empty.</p>
      <a href="shop.php" class="btn btn-dark">Go to Shop</a>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th style="width:120px;">Quantity</th>
              <th>Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): ?>
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'img/products/default-watch.jpg'); ?>" 
                         alt="" style="width:60px; height:60px; object-fit:contain; margin-right:12px;">
                    <div>
                      <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                      <small>Stock: <?php echo (int)$item['stock']; ?></small>
                    </div>
                  </div>
                </td>
                <td>₱<?php echo number_format($item['price'], 2); ?></td>
                <td>
                  <form action="cart-update.php" method="post" class="d-flex">
                    <input type="hidden" name="cart_id" value="<?php echo (int)$item['cart_id']; ?>">
                    <input type="number" name="quantity" min="1" max="<?php echo (int)$item['stock']; ?>"
                           value="<?php echo (int)$item['quantity']; ?>" class="form-control form-control-sm me-2">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                  </form>
                </td>
                <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
                <td>
                  <a href="cart-remove.php?cart_id=<?php echo (int)$item['cart_id']; ?>" 
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Remove this item from cart?');">Remove</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-4">
        <a href="shop.php" class="btn btn-outline-secondary">Continue Shopping</a>
        <div>
          <h4 class="mb-3">Total: ₱<?php echo number_format($total, 2); ?></h4>
          <a href="checkout.php" class="btn btn-dark btn-lg">Proceed to Checkout</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
<?php if ($conn) $conn->close(); ?>

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
    // Use stored procedure GetUserCart to fetch base cart rows
    $proc = $conn->prepare("CALL GetUserCart(?)");
    if ($proc) {
        $proc->bind_param('i', $user_id);
        $proc->execute();

        $procRows = [];
        if (method_exists($proc, 'get_result')) {
            $res = $proc->get_result();
            while ($row = $res->fetch_assoc()) {
                $row['subtotal'] = isset($row['total_price']) ? (float)$row['total_price'] : ((float)$row['price'] * (int)$row['quantity']);
                $procRows[] = $row;
            }
            if (isset($res)) $res->free();
        } else {
            // fallback when mysqlnd not available
            $proc->bind_result($cart_id, $product_name, $price, $quantity, $total_price);
            while ($proc->fetch()) {
                $procRows[] = [
                    'cart_id' => $cart_id,
                    'product_name' => $product_name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $total_price,
                ];
            }
        }

        $proc->close();
        while ($conn->more_results() && $conn->next_result()) {
            $extra = $conn->use_result();
            if ($extra) $extra->free();
        }

        foreach ($procRows as $row) {
            $cart_id = (int)($row['cart_id'] ?? 0);
            if ($cart_id) {
                $pstmt = $conn->prepare("SELECT p.product_id, p.image_url, p.stock FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.cart_id = ?");
                if ($pstmt) {
                    $pstmt->bind_param('i', $cart_id);
                    $pstmt->execute();
                    if (method_exists($pstmt, 'get_result')) {
                        $pres = $pstmt->get_result();
                        if ($pres) {
                            $pinfo = $pres->fetch_assoc();
                            $row['product_id'] = $pinfo['product_id'] ?? null;
                            $row['image_url']  = $pinfo['image_url'] ?? null;
                            $row['stock']      = isset($pinfo['stock']) ? (int)$pinfo['stock'] : 0;
                            $pres->free();
                        }
                    } else {
                        $pstmt->bind_result($ppid, $pimg, $pstock);
                        if ($pstmt->fetch()) {
                            $row['product_id'] = $ppid;
                            $row['image_url'] = $pimg;
                            $row['stock'] = (int)$pstock;
                        }
                    }
                    $pstmt->close();
                }
            } else {
                $row['product_id'] = null;
                $row['image_url'] = null;
                $row['stock'] = 0;
            }
            $cartItems[] = $row;
        }
    }

    // calculate cart total using stored procedure
    $calc = $conn->prepare("CALL CalculateCartTotal(?, @cart_total)");
    if ($calc) {
        $calc->bind_param('i', $user_id);
        $calc->execute();
        $calc->close();

        $out = $conn->query("SELECT @cart_total AS total_amount");
        if ($out) {
            $r = $out->fetch_assoc();
            $total = (float)($r['total_amount'] ?? 0.00);
            $out->free();
        }
        while ($conn->more_results() && $conn->next_result()) {
            $extra = $conn->use_result();
            if ($extra) $extra->free();
        }
    } else {
        foreach ($cartItems as $item) {
            $total += ($item['subtotal'] ?? 0);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Zeit - Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="icon" type="image/x-icon" href="img/icon.png">
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

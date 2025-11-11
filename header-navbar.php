<?php
// avoid duplicate session_start() warnings
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'config.php';
$conn = getDBConnection($host, $user, $password, $database, $port);

$profileHref = !empty($_SESSION['user_id']) ? 'profile.php' : 'login.php';
$cartCount = 'SELECT COUNT(*) AS item_count FROM cart WHERE user_id = ?';
$stmt = $conn->prepare($cartCount);

if ($stmt) {
    if (!empty($_SESSION['user_id'])) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $itemCount = (int)$row['item_count'];
    } else {
        $itemCount = 0;
    }
    $stmt->close();
} else {
    $itemCount = 0;
}
?>
<div class="navbar">
  <a href="index.php"><h1>Zeit</h1></a>

  <div class="nav-links">
    <a href="shop.php">Shop</a>
    <a href="index.php#new-arrivals">New Arrivals</a>
    <a href="about.php">About Us</a>
    <a href="about.php">Brands</a>
  </div>

  <div class="icons">
    <a href="<?php echo $profileHref; ?>"><img src="img/profile-icon.svg" alt="Profile"></a>

    <div class="icons-group">
      <a href="cart.php" class="cart-link">
        <img src="img/cart-icon.svg" alt="Cart">
        <span class="cart-count"><?php echo (int)$itemCount; ?></span>
      </a>

      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="logout.php" class="logout-btn"><img src="img/logout.svg" alt="Logout"></a>
      <?php endif; ?>
    </div>
  </div>
</div>
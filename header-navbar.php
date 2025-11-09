<?php
session_start();
require_once 'config.php';
$conn = getDBConnection($host, $user, $password, $database, $port);

$profileHref = !empty($_SESSION['user_id']) ? 'profile.php' : 'login.php';
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
    <div style="position: relative;">
      <a href="cart.php"><img src="img/cart-icon.svg" alt="Cart"></a>
      <span class="cart-count">0</span>
    </div>
  </div>
</div>
<?php
require_once 'config.php';
$conn = getDBConnection($host, $user, $password, $database, $port);
?>
<div class="navbar">
  <a href="index.php"><h1>Zeit</h1></a>

  <div class="nav-links">
    <a href="shop.php">Shop</a>
    <a href="#new-arrivals">New Arrivals</a>
    <a href="about.php">About Us</a>
    <a href="about.php">Brands</a>
  </div>

  <div class="icons">
    <a href="login.php"><img src="img/profile-icon.svg" alt="Profile"></a>
    <div style="position: relative;">
      <a href="cart.php"><img src="img/cart-icon.svg" alt="Cart"></a>
      <span class="cart-count">0</span>
    </div>
  </div>
</div>
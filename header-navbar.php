<?php
echo '
        <div class="navbar">
          <h1 onclick="window.location.href=\'index.php\'">Zeit</h1>
          
          <div class="nav-links">
            <a href="shop.php">Shop</a>
            <a href="#new-arrivals">New Arrivals</a>
            <a href="about.php">About Us</a>
            <a href="brands.php">Brands</a>
          </div>

          <div class="icons">
            <a href="login.php"><img src="img/profile-icon.svg" alt="Profile"></a>
            <div style="position: relative;">
              <img src="img/cart-icon.svg" alt="Cart" href="cart.php">
              <span class="cart-count">0</span>
            </div>
          </div>
        </div>
';
?>
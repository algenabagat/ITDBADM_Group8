<?php 
  require_once 'config.php';
  $conn = getDBConnection($host, $user, $password, $database, $port);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeit - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/shop.css">
</head>
  <body>
    <div class="container-fluid">
    <?php require 'header-navbar.php' ?>
    <div class="shop-container">
      <div class="filter-bar">
          <div class ="filter-current">
            FILTER: <span class="current-filter">All</span>
          </div>
        <div class="filter-line-border"></div>
        <div class="filter-row">
          <!-- Backend WIP: Will be done before monday -->
          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Brands</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Type</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Gender</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Price</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Dial Color</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Dial Shape</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Dial Type</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Strap Color</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Strap Material</option>
              <option value=""> </option>
            </select>
          </div>

          <div class="filter-item">
            <select class="filter-dropdown">
              <option value="">Style</option>
              <option value=""> </option>
            </select>
          </div>

        </div>
      </div>
      <?php
      require_once 'config.php';
      $conn = getDBConnection($host, $user, $password, $database, $port);

      $totalProducts = 0;
      $displayStart = 1;
      $displayedCount = 0;
      $displayEnd = 0;

      if ($conn) {
          $cntRes = $conn->query("SELECT COUNT(*) AS cnt FROM products");
          if ($cntRes) {
              $totalProducts = (int)($cntRes->fetch_assoc()['cnt'] ?? 0);
              $cntRes->free();
          }

          $sql = "SELECT product_id, product_name, price, image_url FROM products ORDER BY date_added DESC";
          $result = $conn->query($sql);

          if ($result && $result->num_rows > 0) {
              $displayedCount = $result->num_rows;
              $displayEnd = $displayStart + $displayedCount - 1;
          }
      }
      ?>

      <div class="products-container">
        <p>
          <?php
            if ($totalProducts === 0) {
                echo "No items found.";
            } else {
                echo "Items {$displayStart} - " . max($displayStart, $displayEnd) . " of {$totalProducts}";
            }
          ?>
        </p>
              <div class="watch-grid">
          <?php
          require_once 'config.php';
          $conn = getDBConnection($host, $user, $password, $database, $port);
          
          if ($conn) {
              // Query to get the 4 most recent products
              $sql = "SELECT product_id, product_name, price, image_url FROM products 
                      ORDER BY date_added DESC";
              
              $result = $conn->query($sql);
              
              if ($result && $result->num_rows > 0) {
                  while($row = $result->fetch_assoc()) {
                      $product_name = htmlspecialchars($row["product_name"]);
                      $price = number_format($row["price"], 2);
                      $image_url = $row["image_url"] ? htmlspecialchars($row["image_url"]) : 'img/products/default-watch.jpg';
                      $product_id = (int)$row['product_id'];
                      
                      echo "
                      <div class='item-card' onclick=\"window.location.href='view-item.php?product_id={$product_id}';\">
                        <div class='card-image'>
                          <img src='{$image_url}' alt='{$product_name}'>
                        </div>
                        <div class='card-content'>
                          <h3 class='product-name'>{$product_name}</h3>
                          <p class='product-price'>₱{$price}</p>
                          <button class='add-to-cart-btn'>Add to Cart</button>
                        </div>
                      </div>";
                  }
              } else {
                  echo "<p>No products found.</p>";
              }
              
              $conn->close();
          } else {
              echo "<p>Unable to load products.</p>";
          }
          ?>
          </div>
          </div>

      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxMx+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
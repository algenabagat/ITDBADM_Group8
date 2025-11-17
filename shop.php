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
    <link rel="icon" type="image/x-icon" href="img/icon.png">
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
          <form method="GET" action="shop.php">
          <div class="filter-item">
            <select name="brands" class="filter-dropdown">
              <option value="">Brands</option>
              <?php
                $brandQuery = "SELECT DISTINCT brand_name FROM brands ORDER BY brand_name ASC";
                if ($result = $conn->query($brandQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $brand = htmlspecialchars($row['brand_name']);
                        echo "<option value='{$brand}'>{$brand}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="categories" class="filter-dropdown">
              <option value="">Categories</option>
              <?php
                $categoryQuery = "SELECT DISTINCT category_name FROM categories ORDER BY category_name ASC";
                if ($result = $conn->query($categoryQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $category = htmlspecialchars($row['category_name']);
                        echo "<option value='{$category}'>{$category}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="gender" class="filter-dropdown">
              <option value="">Gender</option>
              <?php
                $genderQuery = "SELECT DISTINCT gender FROM products ORDER BY gender ASC";
                if ($result = $conn->query($genderQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $gender = htmlspecialchars($row['gender']);
                        echo "<option value='{$gender}'>{$gender}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="price" class="filter-dropdown">
              <option value="">Price</option>
              <?php
                $priceQuery = "SELECT DISTINCT price FROM products ORDER BY price ASC";
                if ($result = $conn->query($priceQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $price = htmlspecialchars($row['price']);
                        echo "<option value='{$price}'>{$price}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="dial_color" class="filter-dropdown">
              <option value="">Dial Color</option>
              <?php
                $dialcolorQuery = "SELECT DISTINCT dial_color FROM products ORDER BY dial_color ASC";
                if ($result = $conn->query($dialcolorQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $dial_color = htmlspecialchars($row['dial_color']);
                        echo "<option value='{$dial_color}'>{$dial_color}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="dial_shape" class="filter-dropdown">
              <option value="">Dial Shape</option>
              <?php
                $dialshapeQuery = "SELECT DISTINCT dial_shape FROM products ORDER BY dial_shape ASC";
                if ($result = $conn->query($dialshapeQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $dial_shape = htmlspecialchars($row['dial_shape']);
                        echo "<option value='{$dial_shape}'>{$dial_shape}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="dial_type" class="filter-dropdown">
              <option value="">Dial Type</option>
              <?php
                $dialtypeQuery = "SELECT DISTINCT dial_type FROM products ORDER BY dial_type ASC";
                if ($result = $conn->query($dialtypeQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $dial_type = htmlspecialchars($row['dial_type']);
                        echo "<option value='{$dial_type}'>{$dial_type}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="strap_color" class="filter-dropdown">
              <option value="">Strap Color</option>
              <?php
                $strapcolorQuery = "SELECT DISTINCT strap_color FROM products ORDER BY strap_color ASC";
                if ($result = $conn->query($strapcolorQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $strap_color = htmlspecialchars($row['strap_color']);
                        echo "<option value='{$strap_color}'>{$strap_color}</option>";
                    }
                }
              ?>
            </select>
          </div>

          <div class="filter-item">
            <select name="strap_material" class="filter-dropdown">
              <option value="">Strap Material</option>
              <?php
                $strapmaterialQuery = "SELECT DISTINCT strap_material FROM products ORDER BY strap_material ASC";
                if ($result = $conn->query($strapmaterialQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $strap_material = htmlspecialchars($row['strap_material']);
                        echo "<option value='{$strap_material}'>{$strap_material}</option>";
                    }
                }
              ?>
            </select>
          </div>
          
          <div class="filter-submit-row">
            <button type="submit" class="filter-submit">FILTER</button>
          </div>
          </form>
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
          
          if ($conn) {

              $filters = [];

              if(!empty($_GET['brands'])) {
                $brand = $conn->real_escape_string($_GET['brands']);
                $filters[] = "brand_name = '{$brand}'";
              }
              
              if(!empty($_GET['categories'])) {
                $category = $conn->real_escape_string($_GET['categories']);
                $filters[] = "category_name = '{$category}'";
              }

              if(!empty($_GET['gender'])) {
                $gender = $conn->real_escape_string($_GET['gender']);
                $filters[] = "gender = '{$gender}'";
              }

              if(!empty($_GET['price'])) {
                $price = $conn->real_escape_string($_GET['price']);
                $filters[] = "price = '{$price}'";
              }
              
              if(!empty($_GET['dial_color'])) {
                $dial_color = $conn->real_escape_string($_GET['dial_color']);
                $filters[] = "dial_color = '{$dial_color}'";
              }
              
              if(!empty($_GET['dial_shape'])) {
                $dial_shape = $conn->real_escape_string($_GET['dial_shape']);
                $filters[] = "dial_shape = '{$dial_shape}'";
              }
              
              if(!empty($_GET['dial_type'])) {
                $dial_type = $conn->real_escape_string($_GET['dial_type']);
                $filters[] = "dial_type = '{$dial_type}'";
              }
              
              if(!empty($_GET['strap_color'])) {
                $strap_color = $conn->real_escape_string($_GET['strap_color']);
                $filters[] = "strap_color = '{$strap_color}'";
              }
              
              if(!empty($_GET['strap_material'])) {
                $strap_material = $conn->real_escape_string($_GET['strap_material']);
                $filters[] = "strap_material = '{$strap_material}'";
              }
              
              // Query to get the 4 most recent products
              $sql = "SELECT product_id, product_name, price, image_url FROM products";
              
              // adds filters if theres any
              if (count($filters) > 0) {
                $sql .= " WHERE " . implode(" AND ", $filters);
              }

              $sql .= " ORDER BY date_added DESC";

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
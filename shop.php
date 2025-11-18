<?php 
  require_once 'config.php';
  $conn = getDBConnection($host, $user, $password, $database, $port);
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeit - Shop</title>
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
            FILTER: 
          </div>
          <div class="filter-line-border"></div>
        <div class="filter-row">
          <form method="GET" action="shop.php" id="filterForm">
          <div class="filter-item">
            <select name="brands" class="filter-dropdown">
              <option value="">Brands</option>
              <?php
                $brandQuery = "SELECT DISTINCT brand_name FROM brands ORDER BY brand_name ASC";
                if ($result = $conn->query($brandQuery)) {
                    while ($row = $result->fetch_assoc()) {
                        $brand = htmlspecialchars($row['brand_name']);
                        $selected = (!empty($_GET['brands']) && $_GET['brands'] == $brand) ? 'selected' : '';
                        echo "<option value='{$brand}' {$selected}>{$brand}</option>";
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
                        $selected = (!empty($_GET['categories']) && $_GET['categories'] == $category) ? 'selected' : '';
                        echo "<option value='{$category}' {$selected}>{$category}</option>";
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
                        $selected = (!empty($_GET['gender']) && $_GET['gender'] == $gender) ? 'selected' : '';
                        echo "<option value='{$gender}' {$selected}>{$gender}</option>";
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
                        $selected = (!empty($_GET['price']) && $_GET['price'] == $price) ? 'selected' : '';
                        echo "<option value='{$price}' {$selected}>₱" . number_format($price, 2) . "</option>";
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
                        $selected = (!empty($_GET['dial_color']) && $_GET['dial_color'] == $dial_color) ? 'selected' : '';
                        echo "<option value='{$dial_color}' {$selected}>{$dial_color}</option>";
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
                        $selected = (!empty($_GET['dial_shape']) && $_GET['dial_shape'] == $dial_shape) ? 'selected' : '';
                        echo "<option value='{$dial_shape}' {$selected}>{$dial_shape}</option>";
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
                        $selected = (!empty($_GET['dial_type']) && $_GET['dial_type'] == $dial_type) ? 'selected' : '';
                        echo "<option value='{$dial_type}' {$selected}>{$dial_type}</option>";
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
                        $selected = (!empty($_GET['strap_color']) && $_GET['strap_color'] == $strap_color) ? 'selected' : '';
                        echo "<option value='{$strap_color}' {$selected}>{$strap_color}</option>";
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
                        $selected = (!empty($_GET['strap_material']) && $_GET['strap_material'] == $strap_material) ? 'selected' : '';
                        echo "<option value='{$strap_material}' {$selected}>{$strap_material}</option>";
                    }
                }
              ?>
            </select>
          </div>
          
          <div class="filter-submit-row">
            <button type="submit" class="filter-submit">FILTER</button>
            <a href="shop.php" class="filter-clear">CLEAR ALL</a>
          </div>
          </form>
        </div>
      </div>
      <?php
      // Get selected branch from session
      $selectedBranchId = $_SESSION['selected_branch_id'] ?? 0;

      $totalProducts = 0;
      $displayStart = 1;
      $displayedCount = 0;
      $displayEnd = 0;

      if ($conn) {
          // Build base query with joins
          $baseSql = "SELECT p.product_id, p.product_name, p.price, p.image_url 
                     FROM products p 
                     LEFT JOIN brands b ON p.brand_id = b.brand_id 
                     LEFT JOIN categories c ON p.category_id = c.category_id 
                     WHERE 1=1";
          
          // Build count query
          $countSql = $baseSql;
          $filters = [];
          $params = [];
          $types = '';

          // Add branch filter
          if ($selectedBranchId > 0) {
              $filters[] = "p.branch_id = ?";
              $params[] = $selectedBranchId;
              $types .= 'i';
          }

          // Add GET filters
          if(!empty($_GET['brands'])) {
              $filters[] = "b.brand_name = ?";
              $params[] = $_GET['brands'];
              $types .= 's';
          }
          
          if(!empty($_GET['categories'])) {
              $filters[] = "c.category_name = ?";
              $params[] = $_GET['categories'];
              $types .= 's';
          }

          if(!empty($_GET['gender'])) {
              $filters[] = "p.gender = ?";
              $params[] = $_GET['gender'];
              $types .= 's';
          }

          if(!empty($_GET['price'])) {
              $filters[] = "p.price = ?";
              $params[] = $_GET['price'];
              $types .= 'd';
          }
          
          if(!empty($_GET['dial_color'])) {
              $filters[] = "p.dial_color = ?";
              $params[] = $_GET['dial_color'];
              $types .= 's';
          }
          
          if(!empty($_GET['dial_shape'])) {
              $filters[] = "p.dial_shape = ?";
              $params[] = $_GET['dial_shape'];
              $types .= 's';
          }
          
          if(!empty($_GET['dial_type'])) {
              $filters[] = "p.dial_type = ?";
              $params[] = $_GET['dial_type'];
              $types .= 's';
          }
          
          if(!empty($_GET['strap_color'])) {
              $filters[] = "p.strap_color = ?";
              $params[] = $_GET['strap_color'];
              $types .= 's';
          }
          
          if(!empty($_GET['strap_material'])) {
              $filters[] = "p.strap_material = ?";
              $params[] = $_GET['strap_material'];
              $types .= 's';
          }

          // Apply filters to count query
          if (count($filters) > 0) {
              $countSql .= " AND " . implode(" AND ", $filters);
          }

          // Execute count query
          $stmt = $conn->prepare($countSql);
          if ($stmt) {
              if (!empty($params)) {
                  $stmt->bind_param($types, ...$params);
              }
              $stmt->execute();
              $cntRes = $stmt->get_result();
              
              if ($cntRes) {
                  $totalProducts = (int)($cntRes->fetch_assoc()['cnt'] ?? 0);
                  $cntRes->free();
              }
              $stmt->close();
          }

          // Build main query
          $sql = $baseSql;
          if (count($filters) > 0) {
              $sql .= " AND " . implode(" AND ", $filters);
          }
          $sql .= " ORDER BY p.date_added DESC";

          // Execute main query
          $stmt = $conn->prepare($sql);
          if ($stmt) {
              if (!empty($params)) {
                  $stmt->bind_param($types, ...$params);
              }
              $stmt->execute();
              $result = $stmt->get_result();

              if ($result && $result->num_rows > 0) {
                  $displayedCount = $result->num_rows;
                  $displayEnd = $displayStart + $displayedCount - 1;
              }
          }
      }
      ?>

      <div class="products-container">
        <p>
          <?php
            if ($totalProducts === 0) {
                echo "No items found for the selected criteria.";
            } else {
                echo "Items {$displayStart} - " . max($displayStart, $displayEnd) . " of {$totalProducts}";
            }
          ?>
        </p>
        <div class="watch-grid">
          <?php
          if ($conn && isset($result) && $result->num_rows > 0) {
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
              echo "<p>No products found for the selected criteria.</p>";
          }
          
          if ($conn) {
              $conn->close();
          }
          ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxMx+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reset dropdowns to their default closed state
        const dropdowns = document.querySelectorAll('.filter-dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.selectedIndex = 0;
        });
        
        const bootstrapDropdowns = document.querySelectorAll('.dropdown-menu.show');
        bootstrapDropdowns.forEach(menu => {
            menu.classList.remove('show');
        });
        
        const selectElements = document.querySelectorAll('select');
        selectElements.forEach(select => {
            select.blur();
        });
    });
    </script>
  </body>
</html>
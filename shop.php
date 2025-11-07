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
      </div>
      <div class="filter-row">
        <select class="filter-dropdown">
          <option value="">Placeholder</option>
        </select>
      </div>
      <ul class="filter-list">
        <li><img src="placeholder" alt="arrow">Placeholder</li>
      </ul>
      <div class="products-container">
        <p> Item 1 - 9 of 4232 </p>
        <div class="watch-grid">
    <?php
    require_once 'config.php';
    $conn = getDBConnection($host, $user, $password, $database, $port);
    
    if ($conn) {
        // Query to get the 4 most recent products
        $sql = "SELECT product_name, price, image_url FROM products 
                ORDER BY date_added DESC";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $product_name = htmlspecialchars($row["product_name"]);
                $price = number_format($row["price"], 2);
                $image_url = $row["image_url"] ? htmlspecialchars($row["image_url"]) : 'img/products/default-watch.jpg';
                
                echo "
                <div class='item-card'>
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
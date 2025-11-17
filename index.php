<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeit - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
</head>
  <body>
    <div class="container-fluid">
      
    <!-- Navbar Section -->
    <?php require 'header-navbar.php' ?>
        
<!-- Carousel Section -->
<div class="carousel-section">
  <div id="watchCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
    <!-- Carousel Indicators -->
    <div class="carousel-indicators">
      <?php
      require_once 'config.php';
      $conn = getDBConnection($host, $user, $password, $database, $port);
      
      if ($conn) {
          $sql = "SELECT product_id FROM products ORDER BY date_added DESC LIMIT 5";
          $result = $conn->query($sql);
          
          if ($result && $result->num_rows > 0) {
              $index = 0;
              while($row = $result->fetch_assoc()) {
                  $active_class = $index === 0 ? 'active' : '';
                  echo "<button type='button' data-bs-target='#watchCarousel' data-bs-slide-to='{$index}' class='{$active_class}' aria-label='Slide {$index}'></button>";
                  $index++;
              }
          }
          $conn->close();
      }
      ?>
    </div>
    
    <div class="carousel-inner">
      <?php
      require_once 'config.php';
      $conn = getDBConnection($host, $user, $password, $database, $port);
      
      if ($conn) {
          $sql = "SELECT product_id, product_name, price, image_url, description 
                  FROM products 
                  ORDER BY date_added DESC 
                  LIMIT 5";
          
          $result = $conn->query($sql);
          
          if ($result && $result->num_rows > 0) {
              $is_first = true;
              while($row = $result->fetch_assoc()) {
                  $product_id   = (int)$row['product_id'];
                  $product_name = htmlspecialchars($row["product_name"]);
                  $price        = number_format($row["price"], 2);
                  $image_url    = $row["image_url"] ? htmlspecialchars($row["image_url"]) : 'img/products/default-watch.jpg';
                  $description  = htmlspecialchars($row["description"] ?? 'No description available.');
                  
                  $active_class = $is_first ? 'active' : '';
                  echo "
                  <div class='carousel-item {$active_class}'>
                    <div class='carousel-content' onclick=\"window.location.href='view-item.php?product_id={$product_id}';\">
                      <div class='carousel-image'>
                        <img src='{$image_url}' alt='{$product_name}'>
                      </div>
                      <div class='carousel-text'>
                        <h2>{$product_name}</h2>
                        <p class='price'>₱{$price}</p>
                        <p class='description'>{$description}</p>
                        
                        <form action='add-to-cart.php' method='POST' onsubmit='event.stopPropagation();'>
                        <input type='hidden' name='product_id' value='{$product_id}'>
                        <input type='hidden' name='quantity' value='1'>
                        <button type='submit' class='add-to-cart-btn'>
                          Add to Cart
                        </button>
                        </form>

                      </div>
                    </div>
                  </div>";
                  $is_first = false;
              }
          } else {
              echo "<div class='carousel-item active'><div class='carousel-content'><p>No products found for carousel.</p></div></div>";
          }
          
          $conn->close();
      } else {
          echo "<div class='carousel-item active'><div class='carousel-content'><p>Unable to load carousel products.</p></div></div>";
      }
      ?>
    </div>
    
    <!-- Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#watchCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#watchCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</div>
        
<!-- New Arrivals Section -->
<div id="new-arrivals" class="new-arrivals-section">
  <h1>New Arrivals</h1>
  <div class='card-section'>
    <?php
    require_once 'config.php';
    $conn = getDBConnection($host, $user, $password, $database, $port);
    
    if ($conn) {
        $sql = "SELECT product_id, product_name, price, image_url FROM products 
                ORDER BY date_added DESC 
                LIMIT 4";
        
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $product_id   = (int)$row['product_id'];
                $product_name = htmlspecialchars($row["product_name"]);
                $price        = number_format($row["price"], 2);
                $image_url    = $row["image_url"] ? htmlspecialchars($row["image_url"]) : 'img/products/default-watch.jpg';
                
                echo "
                <div class='item-card' onclick=\"window.location.href='view-item.php?product_id={$product_id}';\">
                  <div class='card-image'>
                    <img src='{$image_url}' alt='{$product_name}'>
                  </div>
                  <div class='card-content'>
                    <h3 class='product-name'>{$product_name}</h3>
                    <p class='product-price'>₱{$price}</p>

                    <form action='add-to-cart.php' method='POST' onsubmit='event.stopPropagation();'>
                      <input type='hidden' name='product_id' value='{$product_id}'>
                      <input type='hidden' name='quantity' value='1'>
                      <button type='submit' class='add-to-cart-btn'>
                        Add to Cart
                      </button>
                    </form>

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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
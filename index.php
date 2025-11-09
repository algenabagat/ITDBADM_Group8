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
          <div id="watchCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <div class="carousel-content">
                  <div class="carousel-image">
                    <img src="https://images.unsplash.com/photo-1523170335258-f5ed11844a49?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Minimalist Chronograph Brown Eco Leather Watch">
                  </div>
                  <div class="carousel-text">
                    <h2>Minimalist<br>Chronograph Brown Eco Leather Watch</h2>
                    <p class="price">$349</p>
                    <p class="description">Aenean urna nunc lorem feugiat magna consectetur ante montes. Sollicitudin neque rhoncus vehicula felis tempor porta quam.</p>
                    <button class="add-to-cart-btn">Add to Cart</button>
                  </div>
                  
                </div>
              </div>
            </div>
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
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
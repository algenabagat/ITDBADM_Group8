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
        <div class="navbar">
          <h1>Zeit</h1>
          
          <div class="nav-links">
            <a href="shop.php">Shop</a>
            <a href="new-arrivals.php">New Arrivals</a>
            <a href="about-us.php">About Us</a>
            <a href="brands.php">Brands</a>
          </div>

          <div class="icons">
            <img src="img/profile-icon.svg" alt="Profile">
            <div style="position: relative;">
              <img src="img/cart-icon.svg" alt="Cart">
              <span class="cart-count">0</span>
            </div>
          </div>
        </div>
        
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
        <div class="new-arrivals-section">
          <h1>New Arrivals</h1>
          <div class='card-section'>
            <!-- Product 1 -->
            <div class='item-card'>
              <div class="card-image"></div>
              <div class="card-content">
                <h3 class="product-name">Zone/Mishevery G</h3>
                <p class="product-price">$159</p>
                <button class="add-to-cart-btn">Add to Cart</button>
              </div>
            </div>
            
            <!-- Product 2 -->
            <div class='item-card'>
              <div class="card-image"></div>
              <div class="card-content">
                <h3 class="product-name">Remedies Authoristic</h3>
                <p class="product-price">$157</p>
                <button class="add-to-cart-btn">Add to Cart</button>
              </div>
            </div>
            
            <!-- Product 3 -->
            <div class='item-card'>
              <div class="card-image"></div>
              <div class="card-content">
                <h3 class="product-name">Sierra Large Dial</h3>
                <p class="product-price">$166</p>
                <button class="add-to-cart-btn">Add to Cart</button>
              </div>
            </div>
            
            <!-- Product 4 -->
            <div class='item-card'>
              <div class="card-image"></div>
              <div class="card-content">
                <h3 class="product-name">Sierra Large Dial</h3>
                <p class="product-price">$166</p>
                <button class="add-to-cart-btn">Add to Cart</button>
              </div>
            </div>
          </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
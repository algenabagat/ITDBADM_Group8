<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeit - About Us</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/about.css">
</head>
  <body>
    <div class="container-fluid">
      <?php require 'header-navbar.php' ?>
      
      <section class="about-hero">
        <div class="hero-content">
          <h1 class="hero-title">Curating Timepieces That Define Moments</h1>
          <p class="hero-subtitle">Your trusted destination for premium pre-owned and new watches</p>
        </div>
      </section>

      <section class="mission-section">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <h2>Our Curatorial Approach</h2>
              <p class="mission-text">
                At Zeit, we specialize in curating exceptional timepieces from the world's most respected watchmakers. 
                Each watch in our collection is carefully selected for its craftsmanship, heritage, and timeless appeal.
              </p>
              <p class="mission-text">
                We bridge the gap between watch enthusiasts and their dream timepieces, offering authenticated, 
                quality-checked watches that deliver both value and prestige.
              </p>
            </div>
            <div class="col-lg-6">
              <div class="mission-image">
                <img src="img/about.jpg" 
                     alt="Luxury Watch Collection" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="values-section">
        <div class="container">
          <h2 class="section-title">Why Choose Zeit</h2>
          <div class="row">
            <div class="col-md-4 value-item">
              <div class="value-icon">🔍</div>
              <h3>Expert Curation</h3>
              <p>Every timepiece undergoes rigorous authentication and quality assessment by our watch experts.</p>
            </div>
            <div class="col-md-4 value-item">
              <div class="value-icon">🛡️</div>
              <h3>Trust & Authenticity</h3>
              <p>Complete transparency with verified authenticity and comprehensive service history for every watch.</p>
            </div>
            <div class="col-md-4 value-item">
              <div class="value-icon">💎</div>
              <h3>Quality Assurance</h3>
              <p>From vintage classics to modern masterpieces, each watch meets our stringent quality standards.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="brands-section">
        <div class="container">
          <h2 class="section-title">Featured Brands</h2>
          <p class="brands-subtitle">We partner with renowned watchmakers to bring you exceptional timepieces</p>
          <div class="row justify-content-center">
            <div class="col-6 col-md-4 col-lg-2 brand-item">
              <div class="brand-logo">
                <span>SEIKO</span>
              </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 brand-item">
              <div class="brand-logo">
                <span>HAMILTON</span>
              </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 brand-item">
              <div class="brand-logo">
                <span>TISSOT</span>
              </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 brand-item">
              <div class="brand-logo">
                <span>ORIENT</span>
              </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2 brand-item">
              <div class="brand-logo">
                <span>CERTINA</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="story-section">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2">
              <h2>Our Journey</h2>
              <p class="story-text">
                Founded in 2025, Zeit began as a passion project by watch enthusiasts who recognized the need for 
                a trusted platform for premium timepieces. What started as a small boutique has evolved into 
                a respected destination for watch collectors and enthusiasts alike.
              </p>
              <p class="story-text">
                Today, we continue to build relationships with both collectors and new watch lovers, 
                helping them discover timepieces that resonate with their personal style and story.
              </p>
            </div>
            <div class="col-lg-6 order-lg-1">
              <div class="story-image">
                <img src="img/about2.jpg" 
                     alt="Watch Collection" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="contact-cta">
        <div class="container">
          <h2>Find Your Perfect Timepiece</h2>
          <p>Browse our curated collection of premium watches</p>
          <div class="cta-buttons">
            <a href="index.php" class="btn btn-primary">Explore Collection</a>
            <a href="#" class="btn btn-outline">Contact Us</a>
          </div>
        </div>
      </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxMx+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
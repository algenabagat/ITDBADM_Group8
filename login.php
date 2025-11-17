<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeit - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/logsign.css">
    <link rel="icon" type="image/x-icon" href="img/icon.png">
</head>
  <body>
    <div class="container-fluid">
      <div class="image-container">
        <img src="img/watch.jpg" alt="Watch Image" class="watch-image">
      </div>
      <div class="right-section">
        <button class="back-btn" onclick="window.location.href='index.php'">
          <img src="img/back-icon.svg" alt="Back" class="back-icon"> Back 
        </button>
        <div class="login-container">
            <h1> Account Login </h1>
            <p class="login-description"> If you are already a member you can login with your email address and password.</p>
            <form action="login-process.php" method="post">
              <div class="mb-3">
                <label for="email" class="form-label">Email <Address></Address></label>
                <input type="text" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="mb-3">
                <input type="checkbox" id="rememberMe" name="rememberMe" value="yes">
                <label for="rememberMe"> Remember Me</label>
              </div>
              <button type="submit" class="login-btn">Login</button>
            </form>
              <div class="sign-up">
                <p> Don't have an account? <a href="signup.php"> Sign Up Here </a> </p>
              </div>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxMx+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
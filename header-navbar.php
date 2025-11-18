<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'config.php';
$conn = getDBConnection($servername, $username, $password, $database, $port);

$profileHref = !empty($_SESSION['user_id']) ? 'profile.php' : 'login.php';
$cartCount = 'SELECT COUNT(*) AS item_count FROM cart WHERE user_id = ?';
$stmt = $conn->prepare($cartCount);

if ($stmt) {
    if (!empty($_SESSION['user_id'])) {
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $itemCount = (int)$row['item_count'];
    } else {
        $itemCount = 0;
    }
    $stmt->close();
} else {
    $itemCount = 0;
}

// Get all branches for dropdown
$branches = [];
$branchQuery = "SELECT branch_id, branch_name FROM branches ORDER BY branch_name";
$branchResult = $conn->query($branchQuery);
if ($branchResult && $branchResult->num_rows > 0) {
    while ($row = $branchResult->fetch_assoc()) {
        $branches[] = $row;
    }
}

// Get selected branch from session or default to all branches (0)
$selectedBranchId = $_SESSION['selected_branch_id'] ?? 0;
$selectedBranchName = "All Stores";
if ($selectedBranchId > 0) {
    foreach ($branches as $branch) {
        if ($branch['branch_id'] == $selectedBranchId) {
            $selectedBranchName = $branch['branch_name'];
            break;
        }
    }
}
?>
<div class="navbar">
  <a href="index.php" class="logo"><h1>Zeit</h1></a>

  <div class="nav-links">
    <a href="shop.php">Shop</a>
    <a href="index.php#new-arrivals">New Arrivals</a>
    <a href="about.php">About Us</a>
    <a href="about.php#brands-section">Brands</a>
  </div>

  <div class="icons">
    <!-- Branch Selection Dropdown -->
    <div class="dropdown branch-dropdown">
      <button class="branch-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php echo htmlspecialchars($selectedBranchName); ?>
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item branch-option" href="#" data-branch-id="0">All Stores</a></li>
        <?php foreach ($branches as $branch): ?>
          <li><a class="dropdown-item branch-option" href="#" data-branch-id="<?php echo $branch['branch_id']; ?>">
            <?php echo htmlspecialchars($branch['branch_name']); ?>
          </a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <a href="<?php echo $profileHref; ?>" class="icon-link">
      <img src="img/profile-icon.svg" alt="Profile">
    </a>

    <div class="icons-group">
      <a href="cart.php" class="cart-link icon-link">
        <img src="img/cart-icon.svg" alt="Cart">
        <?php if ($itemCount > 0): ?>
          <span class="cart-count"><?php echo (int)$itemCount; ?></span>
        <?php endif; ?>
      </a>

      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="logout.php" class="logout-btn icon-link">
          <img src="img/logout.svg" alt="Logout">
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle branch selection
    const branchOptions = document.querySelectorAll('.branch-option');
    branchOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const branchId = this.getAttribute('data-branch-id');
            
            // Send AJAX request to update session
            fetch('set-branch.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'branch_id=' + branchId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to reflect the branch change
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>
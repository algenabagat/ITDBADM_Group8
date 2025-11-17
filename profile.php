<?php
// server-side logic first
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'config.php';

// require login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$tab = $_GET['tab'] ?? 'overview';
$user_id = (int)$_SESSION['user_id'];

$conn = getDBConnection($host, $user, $password, $database, $port);

// fetch user info
$userData = null;
if ($conn) {
    $uStmt = $conn->prepare('SELECT first_name, last_name, email, phone FROM users WHERE user_id = ? LIMIT 1');
    if ($uStmt) {
        $uStmt->bind_param('i', $user_id);
        $uStmt->execute();
        $uRes = $uStmt->get_result();
        $userData = $uRes->fetch_assoc() ?: null;
        $uStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zeit - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/profile.css">
    <link rel="icon" type="image/x-icon" href="img/icon.png">
  </head>
<body>
  <div class="container-fluid">
    <?php include 'header-navbar.php'; ?>

    <div class="profile-container">
      <div class="profile-header">
        <h2>Welcome, <?php echo htmlspecialchars($userData['first_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></h2>
        <p>Manage your account and view your orders</p>
      </div>
      
      <div class="profile-content">
        <aside class="sidebar profile-sidebar">
          <h3>User Profile</h3>
          <ul class="profile-menu">
            <li><a class="<?php echo $tab === 'overview' ? 'active' : ''; ?>" href="profile.php?tab=overview"><i class="fas fa-user-circle"></i> Personal Details</a></li>
            <li><a class="<?php echo $tab === 'orders' ? 'active' : ''; ?>" href="profile.php?tab=orders"><i class="fas fa-history"></i> Order History</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </aside>

        <main class="profile-main">
          <?php if ($tab === 'overview'): ?>

            <div class="welcome-message">
              <h4>Welcome, <?php echo htmlspecialchars($userData['first_name'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></h4>
            </div>
            
            <h3 class="section-title">Personal Details</h3>
            
            <?php if ($userData): ?>
              <div class="info-card">
                <div class="info-row">
                  <div class="info-label">First Name:</div>
                  <div class="info-value"><?php echo htmlspecialchars($userData['first_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="info-row">
                  <div class="info-label">Last Name:</div>
                  <div class="info-value"><?php echo htmlspecialchars($userData['last_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="info-row">
                  <div class="info-label">Email:</div>
                  <div class="info-value"><?php echo htmlspecialchars($userData['email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div class="info-row">
                  <div class="info-label">Phone:</div>
                  <div class="info-value"><?php echo htmlspecialchars($userData['phone'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
              </div>
            <?php else: ?>
              <p>User information not available.</p>
            <?php endif; ?>

          <?php elseif ($tab === 'orders'): ?>

            <h2 class="section-title">My Orders</h2>

            <?php
            if ($conn) {
                $oStmt = $conn->prepare('SELECT order_id, total_amount, status, order_date FROM orders WHERE user_id = ? ORDER BY order_date DESC');
                if ($oStmt) {
                    $oStmt->bind_param('i', $user_id);
                    $oStmt->execute();
                    $oRes = $oStmt->get_result();

                    if ($oRes && $oRes->num_rows > 0) {
                        echo '<div class="table-responsive"><table class="table">';
                        echo '<thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>';
                        while ($order = $oRes->fetch_assoc()) {
                            $oid = (int)$order['order_id'];
                            $date = htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8');
                            $total = number_format((float)$order['total_amount'], 2);
                            $status = htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8');
                            
                            $statusClass = '';
                            if ($status === 'pending') $statusClass = 'status-pending';
                            if ($status === 'completed') $statusClass = 'status-completed';
                            if ($status === 'shipped') $statusClass = 'status-shipped';
                            
                            echo "<tr>
                                    <td>#{$oid}</td>
                                    <td>{$date}</td>
                                    <td>₱{$total}</td>
                                    <td><span class=\"status-badge {$statusClass}\">{$status}</span></td>
                                    <td><a href=\"order-details.php?order_id={$oid}\" class=\"btn btn-sm btn-outline-primary\">View</a></td>
                                  </tr>";
                        }
                        echo '</tbody></table></div>';
                    } else {
                        echo '<p>No orders made yet.</p>';
                    }
                    $oStmt->close();
                } else {
                    echo '<p>Unable to load orders.</p>';
                }
            } else {
                echo '<p>Unable to load orders.</p>';
            }
            ?>

          <?php else: ?>

            <p>Unknown tab.</p>

          <?php endif; ?>
        </main>
      </div>
    </div>
  </div>

  <?php if ($conn) $conn->close(); ?>
</body>
</html>
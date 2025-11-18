<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: profile.php");
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id  = (int)$_SESSION['user_id'];

$conn = getDBConnection($host, $user, $password, $database, $port);
if (!$conn) {
    header("Location: profile.php?error=" . urlencode("Database connection error"));
    exit;
}

/* Ownership check (minimal) */
$chk = $conn->prepare("SELECT user_id FROM orders WHERE order_id = ? AND user_id = ?");
if (!$chk) {
    $conn->close();
    header("Location: profile.php?error=" . urlencode("Server error"));
    exit;
}
$chk->bind_param("ii", $order_id, $user_id);
$chk->execute();
$chk->store_result();
if ($chk->num_rows === 0) {
    $chk->close();
    $conn->close();
    header("Location: profile.php?error=" . urlencode("Order not found"));
    exit;
}
$chk->close();

/* Call stored procedure GetOrderDetails to fetch order header + items */
$order = null;
$items = [];

$stmt = $conn->prepare("CALL GetOrderDetails(?)");
if ($stmt) {
    $stmt->bind_param('i', $order_id);
    $stmt->execute();

    if (method_exists($stmt, 'get_result')) {
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            if (!$order) {
                $order = [
                    'order_id' => $r['order_id'],
                    'order_date' => $r['order_date'],
                    'status' => $r['status'],
                    'total_amount' => $r['total_amount'],
                    'customer' => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
                    'branch_name' => $r['branch_name'] ?? ''
                ];
            }
            $items[] = [
                'product_name' => $r['product_name'],
                'price' => $r['item_price'],
                'quantity' => $r['quantity']
            ];
        }
        if (isset($res)) $res->free();
    } else {
        /* fallback bind_result for environments without mysqlnd */
        $stmt->bind_result($oid, $odate, $ostatus, $ototal, $fname, $lname, $bname, $pname, $qty, $iprice);
        while ($stmt->fetch()) {
            if (!$order) {
                $order = [
                    'order_id' => $oid,
                    'order_date' => $odate,
                    'status' => $ostatus,
                    'total_amount' => $ototal,
                    'customer' => trim(($fname ?? '') . ' ' . ($lname ?? '')),
                    'branch_name' => $bname ?? ''
                ];
            }
            $items[] = [
                'product_name' => $pname,
                'price' => $iprice,
                'quantity' => $qty
            ];
        }
    }

    $stmt->close();
    /* drain extra resultsets to avoid "commands out of sync" */
    while ($conn->more_results() && $conn->next_result()) {
        $extra = $conn->use_result();
        if ($extra) $extra->free();
    }
}

/* Preserve original design: fetch image_url for each product (stored proc doesn't return it).
   Do this after closing/draining the proc results to avoid "commands out of sync". */
if (!empty($items)) {
    $imgStmt = $conn->prepare("SELECT image_url FROM products WHERE product_name = ? LIMIT 1");
    foreach ($items as &$it) {
        $it['image_url'] = '';
        if ($imgStmt) {
            $imgStmt->bind_param('s', $it['product_name']);
            $imgStmt->execute();
            if (method_exists($imgStmt, 'get_result')) {
                $imgRes = $imgStmt->get_result();
                if ($imgRow = $imgRes->fetch_assoc()) {
                    $it['image_url'] = $imgRow['image_url'] ?? '';
                }
                if (isset($imgRes)) $imgRes->free();
            } else {
                $imgStmt->bind_result($iurl);
                if ($imgStmt->fetch()) $it['image_url'] = $iurl ?? '';
            }
            $imgStmt->free_result();
        }
    }
    if ($imgStmt) $imgStmt->close();
}

$conn->close();

if (!$order) {
    header("Location: profile.php?error=" . urlencode("Order not found"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order #<?php echo $order_id; ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/icon.png">
</head>

<body class="bg-light">

<div class="container py-5">

    <h2 class="mb-4">Order #<?php echo $order_id; ?></h2>

    <div class="card p-4 mb-4">
        <h4>Order Summary</h4>
        <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>
        <p>Total Amount: <strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></p>
        <p><?php echo date("F d, Y h:i A", strtotime($order['order_date'])); ?></p>
        <?php if (!empty($order['branch_name'])): ?>
            <p>Branch: <strong><?php echo htmlspecialchars($order['branch_name']); ?></strong></p>
        <?php endif; ?>
    </div>

    <div class="card p-4">
        <h4>Items</h4>
        <table class="table">
            <thead>
                <tr>
                    <th style="width:80px;">Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>" width="60" alt="">
                    </td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="profile.php" class="btn btn-secondary mt-4">Back to Orders</a>

</div>

</body>
</html>

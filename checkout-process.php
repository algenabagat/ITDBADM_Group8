<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$full_name = trim($_POST['full_name'] ?? '');
$address   = trim($_POST['address'] ?? '');
$phone     = trim($_POST['phone'] ?? '');
$email     = trim($_POST['email'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$confirm_total  = (float)($_POST['confirm_total'] ?? 0);

if ($full_name === '' || $address === '' || $phone === '' || $email === '' || $payment_method === '') {
    header("Location: checkout.php?error=" . urlencode("Please fill in all required fields."));
    exit;
}

$conn = getDBConnection($servername, $username, $password, $database, $port);

// fetch cart again 
$cartItems = [];
$total = 0.00;

$sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.price, p.branch_id
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $total += $row['subtotal'];
    $cartItems[] = $row;
}
$stmt->close();

if (empty($cartItems)) {
    $conn->close();
    header("Location: cart.php?error=" . urlencode("Your cart is empty."));
    exit;
}

$branch_id = (int)$cartItems[0]['branch_id'];

$conn->begin_transaction();

try {
    // Insert order
    $status = 'Pending';
    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, branch_id, total_amount, status) VALUES (?, ?, ?, ?)");
    $orderStmt->bind_param("iids", $user_id, $branch_id, $total, $status);
    $orderStmt->execute();
    $order_id = $orderStmt->insert_id;
    $orderStmt->close();

    // Insert order_items
    $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $pid = (int)$item['product_id'];
        $qty = (int)$item['quantity'];
        $price = (float)$item['price'];
        $itemStmt->bind_param("iiid", $order_id, $pid, $qty, $price);
        $itemStmt->execute();
    }
    $itemStmt->close();

    // Reduce product stock immediately after inserting order_items
    $stockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
    foreach ($cartItems as $item) {
        $qty = (int)$item['quantity'];
        $pid = (int)$item['product_id'];
        $stockStmt->bind_param("ii", $qty, $pid);
        $stockStmt->execute();
    }
    $stockStmt->close();

    // Create payment record 
    // map payment_method string -> payment_method_id in payment_methods table
    $pmMap = [
        'Cash'  => 1,
        'COD'   => 1,
        'Credit Card' => 2,
        'Card'  => 2,
        'PayPal'=> 3,
        'GCash' => 4,
    ];
    $payment_method_id = $pmMap[$payment_method] ?? 1;
    $currency_id = 1; 
    $payStatus = 'Pending';

    $payStmt = $conn->prepare("INSERT INTO payments (order_id, amount, payment_method_id, currency_id, status) VALUES (?, ?, ?, ?, ?)");
    $payStmt->bind_param("idiis", $order_id, $total, $payment_method_id, $currency_id, $payStatus);
    $payStmt->execute();
    $payStmt->close();

    // Clear cart
    $delStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $delStmt->bind_param("i", $user_id);
    $delStmt->execute();
    $delStmt->close();

    $conn->commit();

    // store last order info in session for summary page
    $_SESSION['last_order'] = [
    'order_id' => $order_id,
    'full_name' => $full_name,
    'address'   => $address,
    'phone'     => $phone,
    'email'     => $email,
    'payment_method' => $payment_method,
    'total'     => $total,

    'currency' => $_POST['currency'] ?? 'PHP',
    'rates' => [
        'PHP' => 1,
        'USD' => 0.018,
        'EUR' => 0.0165
    ],
    'symbol' => ($_POST['currency'] ?? 'PHP') === 'PHP' ? '₱' : (($_POST['currency'] ?? 'PHP') === 'USD' ? '$' : '€'),
    'converted_total' => $total * (($_POST['currency'] ?? 'PHP') === 'PHP' ? 1 : (($_POST['currency'] ?? 'PHP') === 'USD' ? 0.018 : 0.0165)),

    ];


    $conn->close();

    header("Location: order-summary.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    header("Location: checkout.php?error=" . urlencode("Something went wrong placing your order."));
    exit;
}

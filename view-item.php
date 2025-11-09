<?php
require_once 'config.php';
$conn = getDBConnection($host, $user, $password, $database, $port);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Item - <?php echo htmlspecialchars($product_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/view-item.css">
</head>
<body>
    <?php include 'header-navbar.php'; ?>
    <div class="container-fluid">
    <?php 
    $conn = getDBConnection($host, $user, $password, $database, $port);
    $query = "SELECT p.product_id, p.product_name, p.description, p.price, p.image_url,
            b.brand_name, p.dial_color, p.dial_shape, p.strap_color, p.strap_material, p.style, p.stock
     FROM products p
     JOIN brands b ON p.brand_id = b.brand_id
     WHERE p.product_id = ? LIMIT 1";   
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_GET['product_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = mysqli_fetch_object($result);
    if ($result && $result->num_rows > 0) {
        echo "<div class='item-view-container'>
                <div class='item-image'>
                    <img src='" . htmlspecialchars($row->image_url) . "' alt='" . htmlspecialchars($row->product_name) . "' class='img-fluid'>
                </div>
                <div class='item-details'>
                    <h2>" . htmlspecialchars($row->product_name) . "</h2>
                    <p>₱" . number_format($row->price, 2) . "</p>
                    <p>" . nl2br(htmlspecialchars($row->description)) . "</p>
                    <p> Brand: " . htmlspecialchars($row->brand_name) .  "</p>
                    <p> Dial Color: " . htmlspecialchars($row->dial_color) .  "</p>
                    <p> Dial Shape: " . htmlspecialchars($row->dial_shape) .  "</p>
                    <p> Strap Color: " . htmlspecialchars($row->strap_color) .  "</p>
                    <p> Strap Material: " . htmlspecialchars($row->strap_material) .  "</p>
                    <p> Style: " . htmlspecialchars($row->style) .  "</p>
                    <p> Stock: " . (int)$row->stock .  "</p>
                </div>
            </div>";

    }
    else {
        echo "<p>Product not found.</p>";
    }
    ?>
    </div>

    
</body>
</html>
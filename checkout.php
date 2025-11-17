<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please log in to checkout."));
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$conn = getDBConnection($host, $user, $password, $database, $port);

$cartItems = [];
$total = 0.00;
$userData = null;

if ($conn) {
    // cart
    $sql = "SELECT c.cart_id, c.quantity, p.product_id, p.product_name, p.price, p.image_url
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

    // user info
    $uStmt = $conn->prepare("SELECT first_name, last_name, email, phone FROM users WHERE user_id = ? LIMIT 1");
    $uStmt->bind_param("i", $user_id);
    $uStmt->execute();
    $uRes = $uStmt->get_result();
    $userData = $uRes->fetch_assoc();
    $uStmt->close();
}

if (empty($cartItems)) {
    if ($conn) $conn->close();
    header("Location: cart.php?error=" . urlencode("Your cart is empty."));
    exit;
}

$fullName = trim(($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''));
$email    = $userData['email'] ?? '';
$phone    = $userData['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Zeit - Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<div class="container-fluid">
  <?php include 'header-navbar.php'; ?>

  <div class="container py-5">
    <h2 class="mb-4">Checkout</h2>

    <form action="checkout-process.php" method="post">
      <div class="row g-4 align-items-start">
        <!-- LEFT: Customer info -->
        <div class="col-md-7">
          <div class="card mb-3">
            <div class="card-header">
              <strong>Shipping Information</strong>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" 
                       value="<?php echo htmlspecialchars($fullName); ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" 
                       value="<?php echo htmlspecialchars($phone); ?>" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($email); ?>" required>
              </div>
            </div>
          </div> 

          <!-- Payment method -->

          <div class="mb-3">
          <label class="form-label">Currency</label>
          <select name="currency" id="currency" class="form-select" required>
              <option value="PHP" selected>PHP (₱)</option>
              <option value="USD">USD ($)</option>
              <option value="EUR">EUR (€)</option>
    </select>
      </div>

      <div class="mb-3">
          <label for="paymentMethod" class="form-label">Select Payment Method</label>
          <select name="payment_method" id="paymentMethod" class="form-select" required>
              <option value="">-- Select --</option>

              <!-- ALWAYS SHOW -->
              <option value="Card" data-allowed="all">Credit Card</option>

              <!-- PHP ONLY -->
              <option value="COD" data-allowed="php">Cash on Delivery</option>
              <option value="GCash" data-allowed="php">GCash</option>
          </select>
      </div>

      <!-- CARD INFO -->
      <div id="card_fields" style="display:none;">
          <div class="mb-3">
              <label class="form-label">Card Number</label>
              <input type="text" name="card_number" class="form-control">
          </div>
          <div class="mb-3">
              <label class="form-label">Expiry (MM/YY)</label>
              <input type="text" name="card_expiry" class="form-control">
          </div>
          <div class="mb-3">
              <label class="form-label">CVV</label>
              <input type="text" name="card_cvv" class="form-control">
          </div>
      </div>

      <!-- GCash INFO -->
      <div id="gcash_fields" style="display:none;">
          <div class="mb-3">
              <label class="form-label">GCash Number</label>
              <input type="text" name="gcash_number" class="form-control">
          </div>
          <div class="mb-3">
              <label class="form-label">Account Name</label>
              <input type="text" name="gcash_name" class="form-control">
          </div>
      </div>

</div>

        <!-- RIGHT: Order summary -->
        <div class="col-md-5">
          <div class="card">
            <div class="card-header">
              <strong>Order Summary</strong>
            </div>
            <div class="card-body">
              <?php foreach ($cartItems as $item): ?>
                <div class="d-flex justify-content-between mb-2">
                  <div>
                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                    <small>Qty: <?php echo (int)$item['quantity']; ?></small>
                  </div>
                  <div class="item-price" data-php="<?php echo $item['subtotal']; ?>">
                  ₱<?php echo number_format($item['subtotal'], 2); ?>
                  </div>

                </div>
              <?php endforeach; ?>
              <hr>
              <div class="d-flex justify-content-between">
                <span>Total:</span>
                <strong id="total_display">₱<?php echo number_format($total, 2); ?></strong>
              </div>
              <input type="hidden" name="confirm_total" value="<?php echo $total; ?>">
              <button type="submit" class="btn btn-dark w-100 mt-4">Place Order</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
const currency = document.getElementById('currency');
const paymentMethod = document.getElementById('paymentMethod');
const gcashFields = document.getElementById('gcash_fields');
const cardFields = document.getElementById('card_fields');

// Show/Hide extra fields when payment method changes
paymentMethod.addEventListener('change', function () {
    gcashFields.style.display = (this.value === "GCash") ? 'block' : 'none';
    cardFields.style.display  = (this.value === "Card")  ? 'block' : 'none';
});

// Currency change logic (PHP = all methods, USD/EUR = card only)
currency.addEventListener('change', function () {
    const curr = this.value;

    // Reset hidden
    [...paymentMethod.options].forEach(opt => opt.hidden = false);

    if (curr === "USD" || curr === "EUR") {
        [...paymentMethod.options].forEach(opt => {
            if (opt.value !== "" && opt.value !== "Card") {
                opt.hidden = true;
            }
        });

        paymentMethod.value = "Card";
        gcashFields.style.display = "none";
        cardFields.style.display = "block";
    } else {
        paymentMethod.value = "";
        gcashFields.style.display = "none";
        cardFields.style.display = "none";
    }

    // ALSO RUN PRICE CONVERSION
    updatePrices();
});
</script>

<script>
// ============================
// PRICE CONVERSION (PER ITEM + TOTAL)
// ============================

// Per-item price elements
const itemPrices = document.querySelectorAll('.item-price'); 
// Total
const totalDisplay = document.getElementById('total_display');
// PHP base total
const phpTotal = <?php echo $total; ?>;

// Conversion rates
const rates = {
    "PHP": 1,
    "USD": 0.018,
    "EUR": 0.0165
};

function updatePrices() {
    const curr = currency.value;
    const rate = rates[curr];

    const symbol =
        curr === "PHP" ? "₱" :
        curr === "USD" ? "$" :
        "€";

    // PER ITEM
    itemPrices.forEach(el => {
        let phpValue = parseFloat(el.dataset.php);
        let converted = phpValue * rate;

        el.innerHTML =
            symbol + converted.toLocaleString(undefined, { minimumFractionDigits: 2 });
    });

    // TOTAL
    const totalConverted = phpTotal * rate;

    totalDisplay.innerHTML =
        symbol + totalConverted.toLocaleString(undefined, { minimumFractionDigits: 2 });

    // SEND TO BACKEND
    document.getElementById("final_currency").value = curr;
}

// Trigger on page load
updatePrices();
</script>



</body>
</html>
<?php if ($conn) $conn->close(); ?>

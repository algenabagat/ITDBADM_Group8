<?php
require_once 'config.php';
$conn = getDBConnection($host, $user, $password, $database, $port);

// get product id
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id <= 0) {
    http_response_code(400);
    echo "Invalid product id.";
    exit;
}

// fetch product and brand
$query = "SELECT p.product_id, p.product_name, p.description, p.price, p.image_url,
                 b.brand_name, p.dial_color, p.dial_shape, p.strap_color, p.strap_material, p.stock, c.category_name
          FROM products p
          JOIN brands b ON p.brand_id = b.brand_id
          JOIN categories c ON p.category_id = c.category_id
          WHERE p.product_id = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    http_response_code(404);
    echo "Product not found.";
    exit;
}

// normalize fields and provide safe defaults to avoid "undefined index" warnings
$product_name = htmlspecialchars($row['product_name'] ?? 'Untitled', ENT_QUOTES, 'UTF-8');
$image_url = htmlspecialchars($row['image_url'] ?: 'img/products/default-watch.jpg', ENT_QUOTES, 'UTF-8');
$description = nl2br(htmlspecialchars($row['description'] ?? 'No description available.', ENT_QUOTES, 'UTF-8'));
$brand_name = htmlspecialchars($row['brand_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');

$dial_color     = htmlspecialchars($row['dial_color'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$dial_shape     = htmlspecialchars($row['dial_shape'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$strap_color    = htmlspecialchars($row['strap_color'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$strap_material = htmlspecialchars($row['strap_material'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
$style          = htmlspecialchars($row['category_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
// stock may be integer; show "N/A" if missing
$stock_display  = isset($row['stock']) && $row['stock'] !== null ? (int)$row['stock'] : 'N/A';

// fetch currencies
$currencies = [];
$curRes = $conn->query("SELECT currency_code, exchange_rate FROM currencies");
if ($curRes) {
    while ($c = $curRes->fetch_assoc()) {
        $currencies[$c['currency_code']] = (float)$c['exchange_rate'];
    }
    $curRes->free();
}

$flagMap = [
    'PHP' => 'img/ph.svg',
    'USD' => 'img/us.svg',
    'EUR' => 'img/eu.svg'
];

// default currency
$defaultCurrency = 'PHP';
$basePrice = (float)$row['price']; // stored in PHP
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>View Item - <?php echo $product_name; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles/styles.css">
<link rel="stylesheet" href="styles/view-item.css">
</head>
<body>
<?php include 'header-navbar.php'; ?>

<div class="container-fluid">
  <div class="item-view-container">
    <div class="left-section">
      <div class="item-image">
        <img src="<?php echo $image_url; ?>" alt="<?php echo $product_name; ?>" class="img-fluid">
      </div>

      <form action="add-to-cart.php" method="POST">
      <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
      <input type="hidden" name="quantity" value="1">
      <button type="submit" class="add-to-cart-btn">Add to Cart</button>
      </form>


    </div>

    <div class="item-details">
      <h2><?php echo $product_name; ?></h2>

      <!-- currency selector -->
      <div class="currency-selector">
        <button id="currencyToggle" class="currency-toggle" aria-expanded="false" type="button">
          <img id="currencyFlag" src="<?php echo $flagMap[$defaultCurrency] ?? 'img/ph.svg'; ?>" alt="<?php echo $defaultCurrency; ?>" class="flag-icon">
          <span id="currencyCode"><?php echo $defaultCurrency; ?></span>
          <span class="chev">▾</span>
        </button>

        <ul id="currencyMenu" class="currency-menu" hidden>
          <?php foreach ($currencies as $code => $rate): 
              $flag = $flagMap[$code] ?? 'img/ph.svg';
              $safeCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
          ?>
            <li class="currency-item" data-code="<?php echo $safeCode; ?>" data-rate="<?php echo $rate; ?>">
              <img src="<?php echo $flag; ?>" alt="<?php echo $safeCode; ?>" class="flag-icon">
              <span class="code"><?php echo $safeCode; ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <p class="item-price" id="itemPrice" data-base-price="<?php echo $basePrice; ?>">₱<?php echo number_format($basePrice, 2); ?></p>

      <p><?php echo $description; ?></p>
      <p> Brand: <?php echo $brand_name; ?></p>
      <p> Dial Color: <?php echo $dial_color; ?></p>
      <p> Dial Shape: <?php echo $dial_shape; ?></p>
      <p> Strap Color: <?php echo $strap_color; ?></p>
      <p> Strap Material: <?php echo $strap_material; ?></p>
      <p> Style: <?php echo $style; ?></p>
      <p> Stock: <?php echo $stock_display; ?></p>
    </div>
  </div>
</div>

<script>
// rates from server
const RATES = <?php echo json_encode($currencies, JSON_THROW_ON_ERROR); ?>;
// symbol map
const SYMBOLS = { 'PHP': '₱', 'USD': '$', 'EUR': '€' };
// flag map for UI
const FLAG_MAP = <?php echo json_encode($flagMap, JSON_THROW_ON_ERROR); ?>;

(function () {
  const basePrice = parseFloat(document.getElementById('itemPrice').dataset.basePrice);
  const priceEl = document.getElementById('itemPrice');
  const toggle = document.getElementById('currencyToggle');
  const menu = document.getElementById('currencyMenu');
  const flagImg = document.getElementById('currencyFlag');
  const codeSpan = document.getElementById('currencyCode');

  // load stored currency or default
  let selected = localStorage.getItem('selectedCurrency') || '<?php echo $defaultCurrency; ?>';
  applyCurrency(selected);

  // toggle menu
  toggle.addEventListener('click', function (e) {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!expanded));
    menu.hidden = expanded;
    e.stopPropagation();
  });

  // click currency item
  document.querySelectorAll('.currency-item').forEach(item => {
    item.addEventListener('click', function () {
      const code = this.dataset.code;
      applyCurrency(code);
      menu.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
    });
  });

  // close on outside click
  document.addEventListener('click', function () {
    menu.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
  });

  // apply currency format and update UI
  function applyCurrency(code) {
    code = code || '<?php echo $defaultCurrency; ?>';
    const rate = RATES[code];
    const symbol = SYMBOLS[code] || code + ' ';
    const flag = FLAG_MAP[code] || FLAG_MAP['PHP'];

    const converted = (typeof rate !== 'undefined') ? (basePrice * parseFloat(rate)) : basePrice;
    const formatted = Number(converted).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    priceEl.textContent = symbol + formatted;
    flagImg.src = flag;
    flagImg.alt = code;
    codeSpan.textContent = code;
    localStorage.setItem('selectedCurrency', code);
  }
})();
</script>
</body>
</html>
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = getDBConnection($host, $user, $password, $database, $port);
$user_id = $_SESSION['user_id'];

$role_query = "SELECT r.role_name, u.first_name, u.last_name 
               FROM users u 
               JOIN roles r ON u.role_id = r.role_id 
               WHERE u.user_id = ?";
$stmt = $conn->prepare($role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data || ($user_data['role_name'] != 'Admin' && $user_data['role_name'] != 'Staff')) {
    header('Location: index.php');
    exit();
}

$is_admin = $user_data['role_name'] == 'Admin';
$user_name = $user_data['first_name'] . ' ' . $user_data['last_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Zeit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/admin-dashboard.css">
    <link rel="icon" type="image/x-icon" href="img/icon.png">
</head>
<body>
    <div class="container-fluid admin-dashboard">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <div class="header-title-group">
                    <h1>Statistics Dashboard</h1>
                    <div class="mode-switcher">
                        <a href="admin-dashboard.php" class="mode-btn">Management</a>
                        <a href="admin-statistics.php" class="mode-btn active">Statistics</a>
                    </div>
                </div>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                    <span class="role-badge <?php echo $is_admin ? 'admin' : 'staff'; ?>">
                        <?php echo $user_data['role_name']; ?>
                    </span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </header>

        <!-- Main Content (No Sidebar for Stats) -->
        <div class="admin-content" style="padding-left: 0;">
            <!-- Sidebar for Stats -->
            <nav class="admin-sidebar">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="#stats-overview" class="nav-link active" data-target="stats-overview">
                            <span class="nav-icon">📈</span>
                            Overview
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#stats-sales" class="nav-link" data-target="stats-sales">
                            <span class="nav-icon">💰</span>
                            Sales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#stats-products" class="nav-link" data-target="stats-products">
                            <span class="nav-icon">📦</span>
                            Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#stats-users" class="nav-link" data-target="stats-users">
                            <span class="nav-icon">👥</span>
                            Users
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Main Panel -->
            <main class="admin-main" style="flex: 1;">
                <!-- Statistics: Overview -->
                <section id="stats-overview" class="content-section stats-section active">
                    <div class="section-header">
                        <h2>Dashboard Overview</h2>
                    </div>
                    <div class="stats-container">
                        <div class="stat-card">
                            <h5>Total Revenue</h5>
                            <p class="stat-number">₱<?php $sum = $conn->query("SELECT SUM(total_amount) as s FROM orders")->fetch_assoc(); echo number_format($sum['s'] ?? 0, 2); ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Total Orders</h5>
                            <p class="stat-number"><?php $cnt = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc(); echo $cnt['c']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Average Amount Spent per Order</h5>
                            <p class="stat-number">₱<?php $sum = $conn->query("SELECT AVG(total_amount) as s FROM orders")->fetch_assoc(); echo number_format($sum['s'] ?? 0, 2); ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Total Amount of Products Sold</h5>
                            <p class="stat-number"><?php $cnt = $conn->query("SELECT SUM(quantity) as c FROM order_items")->fetch_assoc(); echo $cnt['c']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Total Amount of Products</h5>
                            <p class="stat-number"><?php $prod = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc(); echo $prod['c']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Total Product Stock</h5>
                            <p class="stat-number"><?php $prod = $conn->query("SELECT SUM(stock) as c FROM products")->fetch_assoc(); echo $prod['c']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Total Users</h5>
                            <p class="stat-number"><?php $users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc(); echo $users['c']; ?></p>
                        </div>
                        <div class="stat-card">
                            <h5>Total Brands</h5>
                            <p class="stat-number"><?php $brands = $conn->query("SELECT COUNT(*) as c FROM brands")->fetch_assoc(); echo $brands['c']; ?></p>
                        </div>
                    </div>
                    <div style="margin-top: 30px;">
                        <div class="section-header">
                            <h3>Monthly Revenue</h3>
                        </div>
                        <div class="stats-filters" style="margin-bottom: 20px;">
                            <div class="filter-group">
                                <label>Month:</label>
                                <select id="monthSelector" class="form-select form-select-sm" onchange="updateMonthlyRevenue()">
                                    <?php
                                    $currentMonth = date('m');
                                    for ($m = 1; $m <= 12; $m++) {
                                        $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                        $selected = ($m == $currentMonth) ? 'selected' : '';
                                        echo "<option value='$m' $selected>" . str_pad($m, 2, '0', STR_PAD_LEFT) . " - $monthName</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Year:</label>
                                <select id="yearSelector" class="form-select form-select-sm" onchange="updateMonthlyRevenue()">
                                    <?php
                                    $currentYear = date('Y');
                                    for ($y = $currentYear - 5; $y <= $currentYear; $y++) {
                                        $selected = ($y == $currentYear) ? 'selected' : '';
                                        echo "<option value='$y' $selected>$y</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="stat-card" style="width: 100%; max-width: 500px;">
                            <h5>Revenue for <span id="selectedMonth"><?php echo date('F Y'); ?></span></h5>
                            <p class="stat-number" id="monthlyRevenueAmount">₱<?php 
                                $currentMonth = date('m');
                                $currentYear = date('Y');
                                $mr = $conn->query("SELECT SUM(total_amount) as s FROM orders WHERE MONTH(order_date) = $currentMonth AND YEAR(order_date) = $currentYear")->fetch_assoc();
                                echo number_format($mr['s'] ?? 0, 2);
                            ?></p>
                        </div>
                    </div>

                    <?php
                    // 1. Get ALL branches that have a record in the revenue table
                    $branchesQuery = $conn->query("
                        SELECT DISTINCT b.branch_id, b.branch_name 
                        FROM branches b 
                        JOIN revenue r ON b.branch_id = r.branch_id 
                        ORDER BY b.branch_name ASC
                    ");
                    
                    // Create an array for the options
                    $branches = [];
                    while ($branch = $branchesQuery->fetch_assoc()) {
                        $branches[] = $branch;
                    }
                    
                    // 2. Set default branch (the first one in the list, if available)
                    $defaultBranchId = $branches[0]['branch_id'] ?? null;
                    $defaultBranchName = $branches[0]['branch_name'] ?? 'N/A';
                    $defaultBranchRevenue = 0;

                    // 3. Get the initial revenue for the default branch
                    if ($defaultBranchId !== null) {
                        $br = $conn->query("
                            SELECT total_revenue AS s 
                            FROM revenue 
                            WHERE branch_id = $defaultBranchId
                        ")->fetch_assoc();
                        $defaultBranchRevenue = $br['s'] ?? 0;
                    }
                    ?>

                    <div style="margin-top: 30px;">
                        <div class="section-header">
                            <h3>Branch Revenue</h3>
                        </div>
                                    
                        <div class="stats-filters" style="margin-bottom: 20px;">
                            <div class="filter-group">
                                <label>Branch:</label>
                                <select id="branchSelector" class="form-select form-select-sm" onchange="updateBranchRevenue()">
                                    <?php 
                                    // Populate the dropdown with the prepared array
                                    foreach ($branches as $branch): 
                                        $selected = ($branch['branch_id'] == $defaultBranchId) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $branch['branch_id']; ?>" <?php echo $selected; ?>>
                                            <?php echo htmlspecialchars($branch['branch_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                                    
                        <div class="stat-card" style="width: 100%; max-width: 500px;">
                            <h5>Revenue for <span id="selectedBranch"><?php echo htmlspecialchars($defaultBranchName); ?></span></h5>
                                    
                            <p class="stat-number" id="branchRevenueAmount">
                                ₱<?php echo number_format($defaultBranchRevenue, 2); ?>
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Statistics: Sales -->
                <section id="stats-sales" class="content-section stats-section hidden">
                    <div class="section-header">
                        <h2>Sales Analytics</h2>
                    </div>
                    <div class="stats-filters">
                        <div class="filter-group">
                            <label>Filter by Customer:</label>
                            <select id="filterCustomer" class="form-select form-select-sm">
                                <option value="">-- All Customers --</option>
                                <?php
                                $customers = $conn->query("SELECT DISTINCT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as customer FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY customer ASC");
                                while ($cust = $customers->fetch_assoc()):
                                ?>
                                <option value="<?php echo $cust['user_id']; ?>"><?php echo htmlspecialchars($cust['customer']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Filter by Status:</label>
                            <select id="filterStatus" class="form-select form-select-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="Pending">Pending</option>
                                <option value="Shipped">Shipped</option>
                                <option value="Delivered">Delivered</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Sort by:</label>
                            <select id="sortBy" class="form-select form-select-sm">
                                <option value="date-desc">Date (Newest First)</option>
                                <option value="date-asc">Date (Oldest First)</option>
                                <option value="amount-desc">Amount (Highest First)</option>
                                <option value="amount-asc">Amount (Lowest First)</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="applySalesFilters()">Apply Filters</button>
                        <button class="btn btn-sm btn-secondary" onclick="resetSalesFilters()">Reset</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="salesTableBody">
                                <?php
                                $sales_query = "SELECT o.order_id, o.user_id, CONCAT(u.first_name, ' ', u.last_name) as customer, o.total_amount, o.order_date, o.status FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC LIMIT 100";
                                $sales_result = $conn->query($sales_query);
                                if ($sales_result && $sales_result->num_rows > 0):
                                    while ($sale = $sales_result->fetch_assoc()):
                                ?>
                                <tr class="sales-row" data-customer="<?php echo $sale['user_id']; ?>" data-status="<?php echo $sale['status']; ?>" data-amount="<?php echo $sale['total_amount']; ?>" data-date="<?php echo $sale['order_date']; ?>">
                                    <td><?php echo $sale['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($sale['customer'] ?? 'N/A'); ?></td>
                                    <td>₱<?php echo number_format($sale['total_amount'] ?? 0, 2); ?></td>
                                    <td><?php echo $sale['order_date'] ?? 'N/A'; ?></td>
                                    <td><span class="status-badge <?php echo strtolower(str_replace(' ', '', $sale['status'] ?? 'unknown')); ?>"><?php echo $sale['status'] ?? 'Unknown'; ?></span></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr><td colspan="5" style="text-align: center; color: #999;">No sales data available</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Statistics: Products -->
                <section id="stats-products" class="content-section stats-section hidden">
                    <div class="section-header">
                        <h2>Product Performance</h2>
                    </div>
                    <div class="stats-filters">
                        <div class="filter-group">
                            <label>Filter by Brand:</label>
                            <select id="filterBrand" class="form-select form-select-sm">
                                <option value="">-- All Brands --</option>
                                <?php
                                $brands = $conn->query("SELECT DISTINCT b.brand_id, b.brand_name FROM brands b JOIN products p ON b.brand_id = p.brand_id ORDER BY b.brand_name ASC");
                                while ($brand = $brands->fetch_assoc()):
                                ?>
                                <option value="<?php echo $brand['brand_id']; ?>"><?php echo htmlspecialchars($brand['brand_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Filter by Product:</label>
                            <input type="text" id="filterProduct" class="form-control form-control-sm" placeholder="Search product name...">
                        </div>
                        <div class="filter-group">
                            <label>Sort by:</label>
                            <select id="sortProductsBy" class="form-select form-select-sm">
                                <option value="sold-desc">Total Sold (Highest First)</option>
                                <option value="sold-asc">Total Sold (Lowest First)</option>
                                <option value="ordered-desc">Times Ordered (Most First)</option>
                                <option value="ordered-asc">Times Ordered (Least First)</option>
                                <option value="revenue-desc">Revenue (Highest First)</option>
                                <option value="revenue-asc">Revenue (Lowest First)</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="applyProductFilters()">Apply Filters</button>
                        <button class="btn btn-sm btn-secondary" onclick="resetProductFilters()">Reset</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Brand</th>
                                    <th>Total Sold</th>
                                    <th>Times Ordered</th>
                                    <th>Total Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="productsTableBody">
                                <?php
                                $prod_query = "SELECT p.product_id, p.product_name, b.brand_id, b.brand_name, COALESCE(SUM(oi.quantity), 0) as total_sold, COUNT(DISTINCT oi.order_id) as times_ordered, COALESCE(SUM(oi.quantity * oi.price), 0) as total_revenue FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id LEFT JOIN order_items oi ON p.product_id = oi.product_id GROUP BY p.product_id, p.product_name, b.brand_id, b.brand_name ORDER BY total_sold DESC";
                                $prod_result = $conn->query($prod_query);
                                if ($prod_result && $prod_result->num_rows > 0):
                                    while ($product = $prod_result->fetch_assoc()):
                                ?>
                                <tr class="product-row" data-brand="<?php echo $product['brand_id'] ?? ''; ?>" data-product="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>" data-sold="<?php echo $product['total_sold']; ?>" data-ordered="<?php echo $product['times_ordered']; ?>" data-revenue="<?php echo $product['total_revenue']; ?>">
                                    <td><?php echo htmlspecialchars($product['product_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $product['total_sold'] ?? 0; ?></td>
                                    <td><?php echo $product['times_ordered'] ?? 0; ?></td>
                                    <td>₱<?php echo number_format($product['total_revenue'] ?? 0, 2); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr><td colspan="5" style="text-align: center; color: #999;">No products available</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Statistics: Users -->
                <section id="stats-users" class="content-section stats-section hidden">
                    <div class="section-header">
                        <h2>User Analytics</h2>
                    </div>
                    <div class="stats-filters">
                        <div class="filter-group">
                            <label>Sort by:</label>
                            <select id="sortUsersBy" class="form-select form-select-sm">
                                <option value="id-asc">User ID (Ascending)</option>
                                <option value="id-desc">User ID (Descending)</option>
                                <option value="spent-desc">Amount Spent (Highest First)</option>
                                <option value="spent-asc">Amount Spent (Lowest First)</option>
                                <option value="orders-desc">Orders (Most First)</option>
                                <option value="orders-asc">Orders (Least First)</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary" onclick="applyUserFilters()">Apply Sort</button>
                        <button class="btn btn-sm btn-secondary" onclick="resetUserFilters()">Reset</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Orders Count</th>
                                    <th>Total Spent</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody">
                                <?php
                                $user_query = "SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as name, r.role_name, COUNT(o.order_id) as order_count, COALESCE(SUM(o.total_amount), 0) as total_spent FROM users u LEFT JOIN roles r ON u.role_id = r.role_id LEFT JOIN orders o ON u.user_id = o.user_id GROUP BY u.user_id ORDER BY total_spent DESC";
                                $user_result = $conn->query($user_query);
                                if ($user_result && $user_result->num_rows > 0):
                                    while ($user_stat = $user_result->fetch_assoc()):
                                ?>
                                <tr class="user-row" data-id="<?php echo $user_stat['user_id']; ?>" data-spent="<?php echo $user_stat['total_spent']; ?>" data-orders="<?php echo $user_stat['order_count']; ?>">
                                    <td><?php echo $user_stat['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user_stat['name'] ?? 'N/A'); ?></td>
                                    <td><span class="role-badge <?php echo strtolower($user_stat['role_name'] ?? 'user'); ?>"><?php echo $user_stat['role_name'] ?? 'User'; ?></span></td>
                                    <td><?php echo $user_stat['order_count'] ?? 0; ?></td>
                                    <td>₱<?php echo number_format($user_stat['total_spent'] ?? 0, 2); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr><td colspan="5" style="text-align: center; color: #999;">No user data available</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store original rows for filtering
        let originalSalesRows = [];
        let originalProductRows = [];
        let originalUserRows = [];

        // Statistics Navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Store original rows when page loads
            originalSalesRows = Array.from(document.querySelectorAll('.sales-row')).map(row => row.cloneNode(true));
            originalProductRows = Array.from(document.querySelectorAll('.product-row')).map(row => row.cloneNode(true));
            originalUserRows = Array.from(document.querySelectorAll('.user-row')).map(row => row.cloneNode(true));

            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');

                    // Update active nav link
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    // Show target section - remove all hidden and active classes first
                    document.querySelectorAll('.content-section').forEach(section => {
                        section.classList.remove('active');
                        section.classList.add('hidden');
                    });
                    
                    // Show the target section
                    const targetSection = document.getElementById(target);
                    if (targetSection) {
                        targetSection.classList.remove('hidden');
                        targetSection.classList.add('active');
                    }
                });
            });
        });

        // Sales Analytics Filters
        function applySalesFilters() {
            const customerFilter = document.getElementById('filterCustomer').value;
            const statusFilter = document.getElementById('filterStatus').value;
            const sortBy = document.getElementById('sortBy').value;

            // Start with original rows
            let rows = originalSalesRows.map(row => row.cloneNode(true));

            // Filter by customer
            if (customerFilter) {
                rows = rows.filter(row => row.getAttribute('data-customer') == customerFilter);
            }

            // Filter by status
            if (statusFilter) {
                rows = rows.filter(row => row.getAttribute('data-status') == statusFilter);
            }

            // Sort
            rows.sort((a, b) => {
                if (sortBy === 'date-desc') {
                    return new Date(b.getAttribute('data-date')) - new Date(a.getAttribute('data-date'));
                } else if (sortBy === 'date-asc') {
                    return new Date(a.getAttribute('data-date')) - new Date(b.getAttribute('data-date'));
                } else if (sortBy === 'amount-desc') {
                    return parseFloat(b.getAttribute('data-amount')) - parseFloat(a.getAttribute('data-amount'));
                } else if (sortBy === 'amount-asc') {
                    return parseFloat(a.getAttribute('data-amount')) - parseFloat(b.getAttribute('data-amount'));
                }
            });

            // Update table
            const tbody = document.getElementById('salesTableBody');
            tbody.innerHTML = '';
            if (rows.length > 0) {
                rows.forEach(row => tbody.appendChild(row));
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No matching sales found</td></tr>';
            }
        }

        function resetSalesFilters() {
            document.getElementById('filterCustomer').value = '';
            document.getElementById('filterStatus').value = '';
            document.getElementById('sortBy').value = 'date-desc';
            applySalesFilters();
        }

        // Product Performance Filters
        function applyProductFilters() {
            const brandFilter = document.getElementById('filterBrand').value;
            const productFilter = document.getElementById('filterProduct').value.toLowerCase();
            const sortBy = document.getElementById('sortProductsBy').value;

            // Start with original rows
            let rows = originalProductRows.map(row => row.cloneNode(true));

            // Filter by brand
            if (brandFilter) {
                rows = rows.filter(row => row.getAttribute('data-brand') == brandFilter);
            }

            // Filter by product name
            if (productFilter) {
                rows = rows.filter(row => row.getAttribute('data-product').toLowerCase().includes(productFilter));
            }

            // Sort
            rows.sort((a, b) => {
                if (sortBy === 'sold-desc') {
                    return parseInt(b.getAttribute('data-sold')) - parseInt(a.getAttribute('data-sold'));
                } else if (sortBy === 'sold-asc') {
                    return parseInt(a.getAttribute('data-sold')) - parseInt(b.getAttribute('data-sold'));
                } else if (sortBy === 'ordered-desc') {
                    return parseInt(b.getAttribute('data-ordered')) - parseInt(a.getAttribute('data-ordered'));
                } else if (sortBy === 'ordered-asc') {
                    return parseInt(a.getAttribute('data-ordered')) - parseInt(b.getAttribute('data-ordered'));
                } else if (sortBy === 'revenue-desc') {
                    return parseFloat(b.getAttribute('data-revenue')) - parseFloat(a.getAttribute('data-revenue'));
                } else if (sortBy === 'revenue-asc') {
                    return parseFloat(a.getAttribute('data-revenue')) - parseFloat(b.getAttribute('data-revenue'));
                }
            });

            // Update table
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = '';
            if (rows.length > 0) {
                rows.forEach(row => tbody.appendChild(row));
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No matching products found</td></tr>';
            }
        }

        function resetProductFilters() {
            document.getElementById('filterBrand').value = '';
            document.getElementById('filterProduct').value = '';
            document.getElementById('sortProductsBy').value = 'sold-desc';
            applyProductFilters();
        }

        // User Analytics Sorting
        function applyUserFilters() {
            const sortBy = document.getElementById('sortUsersBy').value;

            // Start with original rows
            let rows = originalUserRows.map(row => row.cloneNode(true));

            // Sort
            rows.sort((a, b) => {
                if (sortBy === 'id-asc') {
                    return parseInt(a.getAttribute('data-id')) - parseInt(b.getAttribute('data-id'));
                } else if (sortBy === 'id-desc') {
                    return parseInt(b.getAttribute('data-id')) - parseInt(a.getAttribute('data-id'));
                } else if (sortBy === 'spent-desc') {
                    return parseFloat(b.getAttribute('data-spent')) - parseFloat(a.getAttribute('data-spent'));
                } else if (sortBy === 'spent-asc') {
                    return parseFloat(a.getAttribute('data-spent')) - parseFloat(b.getAttribute('data-spent'));
                } else if (sortBy === 'orders-desc') {
                    return parseInt(b.getAttribute('data-orders')) - parseInt(a.getAttribute('data-orders'));
                } else if (sortBy === 'orders-asc') {
                    return parseInt(a.getAttribute('data-orders')) - parseInt(b.getAttribute('data-orders'));
                }
            });

            // Update table
            const tbody = document.getElementById('usersTableBody');
            tbody.innerHTML = '';
            if (rows.length > 0) {
                rows.forEach(row => tbody.appendChild(row));
            } else {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999;">No user data available</td></tr>';
            }
        }

        function resetUserFilters() {
            document.getElementById('sortUsersBy').value = 'id-asc';
            applyUserFilters();
        }

        // Monthly Revenue Update
        function updateMonthlyRevenue() {
            const month = document.getElementById('monthSelector').value;
            const year = document.getElementById('yearSelector').value;

            // Send request to fetch monthly revenue
            fetch('get-monthly-revenue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    month: month,
                    year: year
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the display
                    const monthName = new Date(year, month - 1, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                    document.getElementById('selectedMonth').textContent = monthName;
                    document.getElementById('monthlyRevenueAmount').textContent = '₱' + parseFloat(data.revenue).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                } else {
                    document.getElementById('monthlyRevenueAmount').textContent = '₱0.00';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('monthlyRevenueAmount').textContent = '₱0.00';
            });
        }

        function updateBranchRevenue() {
            const select = document.getElementById("branchSelector");
            const branchId = select.value;
            const selectedBranchName = select.options[select.selectedIndex].text;
                
            // Update the branch name immediately for a better user experience
            document.getElementById("selectedBranch").textContent = selectedBranchName;

            // Send request to fetch branch revenue
            fetch('get-branch-revenue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    branch_id: branchId
                })
            })
            .then(response => response.json())
            .then(data => {
                const revenueElement = document.getElementById('branchRevenueAmount');
                if (data.success) {
                    // Update the revenue display
                    const formattedRevenue = parseFloat(data.revenue).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    revenueElement.textContent = '₱' + formattedRevenue;
                } else {
                    console.error('Error fetching branch revenue:', data.message);
                    revenueElement.textContent = '₱0.00';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('branchRevenueAmount').textContent = '₱0.00';
            });
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>

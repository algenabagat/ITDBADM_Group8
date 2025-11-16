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
    <title>Admin Dashboard - Zeit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/styles.css">
    <link rel="stylesheet" href="styles/admin-dashboard.css">
</head>
<body>
    <div class="container-fluid admin-dashboard">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-content">
                <h1>Admin Dashboard</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user_name); ?></span>
                    <span class="role-badge <?php echo $is_admin ? 'admin' : 'staff'; ?>">
                        <?php echo $user_data['role_name']; ?>
                    </span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Sidebar -->
            <nav class="admin-sidebar">
                <ul class="nav-menu">
                    <?php if ($is_admin): ?>
                    <li class="nav-item">
                        <a href="#users" class="nav-link active" data-target="users">
                            <span class="nav-icon">👥</span>
                            Users Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#orders" class="nav-link" data-target="orders">
                            <span class="nav-icon">📦</span>
                            Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#payments" class="nav-link" data-target="payments">
                            <span class="nav-icon">💳</span>
                            Payments
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a href="#products" class="nav-link" data-target="products">
                            <span class="nav-icon">⌚</span>
                            Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#inventory" class="nav-link" data-target="inventory">
                            <span class="nav-icon">📊</span>
                            Inventory
                        </a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li class="nav-item">
                        <a href="#brands" class="nav-link" data-target="brands">
                            <span class="nav-icon">🏷️</span>
                            Brands
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#categories" class="nav-link" data-target="categories">
                            <span class="nav-icon">📁</span>
                            Categories
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Main Panel -->
            <main class="admin-main">
                <!-- Users Management -->
                <section id="users" class="content-section active">
                    <div class="section-header">
                        <h2>Users Management</h2>
                        <button class="btn btn-primary" onclick="openModal('addUser')">Add User</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $users_query = "SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id";
                                $users_result = $conn->query($users_query);
                                while ($user = $users_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="role-badge <?php echo strtolower($user['role_name']); ?>"><?php echo $user['role_name']; ?></span></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                    <td><?php echo $user['created_at']; ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editUser(<?php echo $user['user_id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-delete" onclick="deleteUser(<?php echo $user['user_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
                <!-- Brands Management -->
                <section id="brands" class="content-section">
                    <div class="section-header">
                        <h2>Brands Management</h2>
                        <button class="btn btn-primary" onclick="openModal('addBrand')">Add Brand</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Brand Name</th>
                                    <th>Country</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $brands_query = "SELECT * FROM brands ORDER BY brand_id ASC";
                                $brands_result = $conn->query($brands_query);
                                while ($brand = $brands_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $brand['brand_id']; ?></td>
                                    <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                    <td><?php echo htmlspecialchars($brand['country'] ?? 'N/A'); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editBrand(<?php echo $brand['brand_id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-delete" onclick="deleteBrand(<?php echo $brand['brand_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Categories Management -->
                <section id="categories" class="content-section">
                    <div class="section-header">
                        <h2>Categories Management</h2>
                        <button class="btn btn-primary" onclick="openModal('addCategory')">Add Category</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $categories_query = "SELECT * FROM categories ORDER BY category_id ASC";
                                $categories_result = $conn->query($categories_query);
                                while ($category = $categories_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $category['category_id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editCategory(<?php echo $category['category_id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-delete" onclick="deleteCategory(<?php echo $category['category_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Products Management -->
                <section id="products" class="content-section">
                    <div class="section-header">
                        <h2>Products Management</h2>
                        <button class="btn btn-primary" onclick="openModal('addProduct')">Add Product</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $products_query = "SELECT p.*, b.brand_name, c.category_name 
                                                 FROM products p 
                                                 LEFT JOIN brands b ON p.brand_id = b.brand_id 
                                                 LEFT JOIN categories c ON p.category_id = c.category_id";
                                $products_result = $conn->query($products_query);
                                while ($product = $products_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $product['product_id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td><span class="status-badge <?php echo $product['is_available'] == 'Yes' ? 'active' : 'inactive'; ?>"><?php echo $product['is_available']; ?></span></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editProduct(<?php echo $product['product_id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-delete" onclick="deleteProduct(<?php echo $product['product_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Inventory Management -->
                <section id="inventory" class="content-section">
                    <div class="section-header">
                        <h2>Inventory Management</h2>
                        <button class="btn btn-primary" onclick="openModal('updateInventory')">Update Stock</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Current Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $inventory_query = "SELECT product_id, product_name, stock, is_available, date_added FROM products";
                                $inventory_result = $conn->query($inventory_query);
                                while ($item = $inventory_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $item['product_id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo $item['stock']; ?></td>
                                    <td><span class="status-badge <?php echo $item['is_available'] == 'Yes' ? 'active' : 'inactive'; ?>"><?php echo $item['is_available']; ?></span></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editStock(<?php echo $item['product_id']; ?>)">Update</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Orders Management -->
                <section id="orders" class="content-section">
                    <div class="section-header">
                            <h2>Orders</h2>
                            <button class="btn btn-primary" onclick="openModal('addOrder')">Add Order</button>
                        </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Branch</th>
                                    <th>Order Date</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Total Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $orders_query = "
                                    SELECT 
                                        o.order_id,
                                        CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                                        b.branch_name,
                                        o.order_date,
                                        p.product_name,
                                        oi.quantity,
                                        o.status,
                                        o.total_amount
                                    FROM orders o
                                    JOIN users u ON o.user_id = u.user_id
                                    LEFT JOIN branches b ON o.branch_id = b.branch_id
                                    JOIN order_items oi ON o.order_id = oi.order_id
                                    JOIN products p ON oi.product_id = p.product_id
                                    ORDER BY o.order_date DESC
                                ";
                                $orders_result = $conn->query($orders_query);
                                while ($order = $orders_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['branch_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $order['order_date']; ?></td>
                                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td><?php echo $order['quantity']; ?></td>
                                    <td><span class=\"status-badge <?php echo strtolower(str_replace(' ', '', $order['status'])); ?>\"><?php echo $order['status']; ?></span></td>
                                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editOrder(<?php echo $order['order_id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-delete" onclick="deleteOrder(<?php echo $order['order_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Payments Management -->
                <section id="payments" class="content-section">
                    <div class="section-header">
                        <h2>Payments</h2>
                        <button class="btn btn-primary" onclick="openModal('addPayment')">Add Payment</button>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Order ID</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Payment Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $payments_query = "SELECT p.*, pm.method_name FROM payments p LEFT JOIN payment_methods pm ON p.payment_method_id = pm.payment_method_id ORDER BY p.payment_date DESC";
                                $payments_result = $conn->query($payments_query);
                                while ($payment = $payments_result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $payment['payment_id']; ?></td>
                                    <td><?php echo $payment['order_id'] ?? 'N/A'; ?></td>
                                    <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($payment['method_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $payment['payment_date']; ?></td>
                                    <td><span class="status-badge <?php echo strtolower(str_replace(' ', '', $payment['status'])); ?>"><?php echo $payment['status']; ?></span></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editPayment(<?php echo $payment['payment_id']; ?>)">Edit</button>
                                        <button class="btn btn-sm btn-delete" onclick="deletePayment(<?php echo $payment['payment_id']; ?>)">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Add Payment Modal -->
                <div id="addPaymentModal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <?php
                                    $orders_for_pay = $conn->query("SELECT order_id FROM orders ORDER BY order_id DESC");
                                    $payment_methods = $conn->query("SELECT payment_method_id, method_name FROM payment_methods ORDER BY payment_method_id ASC");
                                ?>
                                <div class="mb-3">
                                    <label class="form-label">Order</label>
                                    <select id="addPaymentOrder" class="form-select">
                                        <option value="">-- Select Order --</option>
                                        <?php while ($o = $orders_for_pay->fetch_assoc()): ?>
                                            <option value="<?php echo $o['order_id']; ?>"><?php echo $o['order_id']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" id="addPaymentAmount" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select id="addPaymentMethod" class="form-select">
                                        <option value="">-- Select Method --</option>
                                        <?php while ($m = $payment_methods->fetch_assoc()): ?>
                                            <option value="<?php echo $m['payment_method_id']; ?>"><?php echo htmlspecialchars($m['method_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="addPaymentStatus" class="form-select">
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="createPayment()">Create Payment</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Payment Modal -->
                <div id="editPaymentModal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editPaymentId">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" id="editPaymentAmount" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select id="editPaymentMethod" class="form-select">
                                        <option value="">-- Select Method --</option>
                                        <?php
                                            $payment_methods2 = $conn->query("SELECT payment_method_id, method_name FROM payment_methods ORDER BY payment_method_id ASC");
                                            while ($m2 = $payment_methods2->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $m2['payment_method_id']; ?>"><?php echo htmlspecialchars($m2['method_name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select id="editPaymentStatus" class="form-select">
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Failed">Failed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="savePayment()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" id="addFirstName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" id="addLastName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" id="addEmail" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" id="addPhone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" id="addPassword" class="form-control" placeholder="Min. 6 characters">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select id="addRole" class="form-select">
                            <option value="1">Admin</option>
                            <option value="2">Staff</option>
                            <option value="3" selected>Customer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createUser()">Create User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" id="editFirstName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" id="editLastName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" id="editEmail" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" id="editPhone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select id="editRole" class="form-select">
                            <option value="1">Admin</option>
                            <option value="2">Staff</option>
                            <option value="3">Customer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="addProductName" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" id="addProductPrice" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Brand</label>
                            <select id="addProductBrand" class="form-select"></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="addProductCategory" class="form-select"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" id="addProductStock" class="form-control" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Available</label>
                            <select id="addProductAvailable" class="form-select">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="addProductDescription" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createProduct()">Create Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProductId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="editProductName" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" id="editProductPrice" class="form-control" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Brand</label>
                            <select id="editProductBrand" class="form-select"></select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="editProductCategory" class="form-select"></select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" id="editProductStock" class="form-control" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Available</label>
                            <select id="editProductAvailable" class="form-select">
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="editProductDescription" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Save Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Stock Modal -->
    <div id="updateStockModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product ID</label>
                        <input type="text" id="productIdInput" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="productNameInput" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="text" id="currentStockInput" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Stock Quantity</label>
                        <input type="number" id="newStockInput" class="form-control" placeholder="Enter new stock quantity" min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveStock()">Update Stock</button>
                </div>
            </div>
        </div>
    </div>

                <!-- Add Brand Modal -->
                <div id="addBrandModal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Brand</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Brand Name</label>
                                    <input type="text" id="addBrandName" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" id="addBrandCountry" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="createBrand()">Create Brand</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Brand Modal -->
                <div id="editBrandModal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Brand</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editBrandId">
                                <div class="mb-3">
                                    <label class="form-label">Brand Name</label>
                                    <input type="text" id="editBrandName" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Country</label>
                                    <input type="text" id="editBrandCountry" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" onclick="saveBrand()">Save</button>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" id="addCategoryName" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createCategory()">Create Category</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editCategoryId">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" id="editCategoryName" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Modals -->
    <!-- View Order Modal -->
    <div id="viewOrderModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="orderInfo">
                        <p><strong>Order ID:</strong> <span id="viewOrderId"></span></p>
                        <p><strong>Customer:</strong> <span id="viewOrderCustomer"></span></p>
                        <p><strong>Branch:</strong> <span id="viewOrderBranch"></span></p>
                        <p><strong>Order Date:</strong> <span id="viewOrderDate"></span></p>
                        <p><strong>Status:</strong> <span id="viewOrderStatus"></span></p>
                        <hr>
                        <h6>Items</h6>
                        <table class="table">
                            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                            <tbody id="viewOrderItems"></tbody>
                        </table>
                        <p class="text-end"><strong>Total:</strong> ₱<span id="viewOrderTotal"></span></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Order Modal (status only) -->
    <div id="editOrderModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editOrderId">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select id="editOrderStatus" class="form-select">
                            <option value="Pending">Pending</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveOrder()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Order Modal -->
    <div id="addOrderModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php
                        // load options for selects
                        $all_users = $conn->query("SELECT user_id, first_name, last_name FROM users");
                        $all_branches = $conn->query("SELECT branch_id, branch_name FROM branches");
                        $all_products = $conn->query("SELECT product_id, product_name, price, stock FROM products WHERE stock > 0");
                    ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select id="createOrderUser" class="form-select">
                                <?php while ($u = $all_users->fetch_assoc()): ?>
                                    <option value="<?php echo $u['user_id']; ?>"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Branch</label>
                            <select id="createOrderBranch" class="form-select">
                                <option value="">-- Select Branch --</option>
                                <?php while ($b = $all_branches->fetch_assoc()): ?>
                                    <option value="<?php echo $b['branch_id']; ?>"><?php echo htmlspecialchars($b['branch_name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div id="orderItemsContainer">
                        <h6>Items</h6>
                        <div class="order-item-row d-flex gap-2 mb-2">
                            <select class="form-select item-product">
                                <option value="">-- Select Product --</option>
                                <?php while ($p = $all_products->fetch_assoc()): ?>
                                    <option data-price="<?php echo $p['price']; ?>" value="<?php echo $p['product_id']; ?>"><?php echo htmlspecialchars($p['product_name'] . ' (₱' . number_format($p['price'],2) .')'); ?></option>
                                <?php endwhile; ?>
                            </select>
                            <input type="number" class="form-control item-qty" placeholder="Qty" min="1" value="1">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItemRow(this)">Remove</button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-primary" type="button" onclick="addItemRow()">Add Item</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createOrder()">Create Order</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load brands and categories on page load
        function loadBrandsAndCategories() {
            Promise.all([
                fetch('get-brands.php').then(res => res.json()),
                fetch('get-categories.php').then(res => res.json())
            ])
            .then(([brandsData, categoriesData]) => {
                if (brandsData.success) {
                    populateBrandDropdowns(brandsData.brands);
                }
                if (categoriesData.success) {
                    populateCategoryDropdowns(categoriesData.categories);
                }
            })
            .catch(err => console.error('Error loading brands/categories:', err));
        }

        function populateBrandDropdowns(brands) {
            const addDropdown = document.getElementById('addProductBrand');
            const editDropdown = document.getElementById('editProductBrand');
            
            [addDropdown, editDropdown].forEach(dropdown => {
                dropdown.innerHTML = '<option value="">-- Select Brand --</option>';
                brands.forEach(brand => {
                    const option = document.createElement('option');
                    option.value = brand.brand_id;
                    option.text = brand.brand_name;
                    dropdown.appendChild(option);
                });
            });
        }

        function populateCategoryDropdowns(categories) {
            const addDropdown = document.getElementById('addProductCategory');
            const editDropdown = document.getElementById('editProductCategory');
            
            [addDropdown, editDropdown].forEach(dropdown => {
                dropdown.innerHTML = '<option value="">-- Select Category --</option>';
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category_id;
                    option.text = category.category_name;
                    dropdown.appendChild(option);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', loadBrandsAndCategories);
        // Navigation functionality
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const target = this.getAttribute('data-target');

                // Update active nav link
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');

                // Show target section
                document.querySelectorAll('.content-section').forEach(section => {
                    section.classList.remove('active');
                });
                document.getElementById(target).classList.add('active');
            });
        });

        function openModal(modalType) {
            if (modalType === 'addUser') {
                openAddUserModal();
            } else if (modalType === 'addProduct') {
                openAddProductModal();
            } else if (modalType === 'addOrder') {
                openAddOrderModal();
            } else if (modalType === 'addBrand') {
                openAddBrandModal();
            } else if (modalType === 'addCategory') {
                openAddCategoryModal();
            } else if (modalType === 'addPayment') {
                openAddPaymentModal();
            } else {
                console.log('Opening modal:', modalType);
            }
        }

        function openAddUserModal() {
            document.getElementById('addFirstName').value = '';
            document.getElementById('addLastName').value = '';
            document.getElementById('addEmail').value = '';
            document.getElementById('addPhone').value = '';
            document.getElementById('addPassword').value = '';
            document.getElementById('addRole').value = '3';
            const modal = new bootstrap.Modal(document.getElementById('addUserModal'));
            modal.show();
        }

        function createUser() {
            const payload = {
                first_name: document.getElementById('addFirstName').value.trim(),
                last_name: document.getElementById('addLastName').value.trim(),
                email: document.getElementById('addEmail').value.trim(),
                phone: document.getElementById('addPhone').value.trim(),
                password: document.getElementById('addPassword').value,
                role_id: document.getElementById('addRole').value
            };

            if (!payload.first_name || !payload.last_name || !payload.email || !payload.password) {
                alert('Please fill in all required fields');
                return;
            }

            if (payload.password.length < 6) {
                alert('Password must be at least 6 characters');
                return;
            }

            fetch('create-user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('User created successfully');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Create failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error creating user');
            });
        }

        // ---- User Management (Edit / Delete) ----
        function editUser(userId) {
            // Fetch user details and populate modal
            fetch('get-user.php?user_id=' + userId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editUserId').value = data.user.user_id;
                        document.getElementById('editFirstName').value = data.user.first_name;
                        document.getElementById('editLastName').value = data.user.last_name;
                        document.getElementById('editEmail').value = data.user.email;
                        document.getElementById('editPhone').value = data.user.phone || '';
                        document.getElementById('editRole').value = data.user.role_id;
                        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                        modal.show();
                    } else {
                        alert('Failed to load user: ' + (data.message || 'unknown'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error loading user details');
                });
        }

        function saveUser() {
            const userId = document.getElementById('editUserId').value;
            const payload = {
                user_id: userId,
                first_name: document.getElementById('editFirstName').value.trim(),
                last_name: document.getElementById('editLastName').value.trim(),
                email: document.getElementById('editEmail').value.trim(),
                phone: document.getElementById('editPhone').value.trim(),
                role_id: document.getElementById('editRole').value
            };

            fetch('update-user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('User updated');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error updating user');
            });
        }

        function deleteUser(userId) {
            if (!confirm('Are you sure you want to delete this user?')) return;

            fetch('delete-user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('User deleted');
                    location.reload();
                } else {
                    alert('Delete failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error deleting user');
            });
        }

        function openAddProductModal() {
            document.getElementById('addProductName').value = '';
            document.getElementById('addProductPrice').value = '';
            document.getElementById('addProductStock').value = '';
            document.getElementById('addProductDescription').value = '';
            document.getElementById('addProductAvailable').value = 'Yes';
            const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
            modal.show();
        }

        function createProduct() {
            const payload = {
                product_name: document.getElementById('addProductName').value.trim(),
                price: parseFloat(document.getElementById('addProductPrice').value),
                brand_id: document.getElementById('addProductBrand').value || null,
                category_id: document.getElementById('addProductCategory').value || null,
                stock: parseInt(document.getElementById('addProductStock').value) || 0,
                description: document.getElementById('addProductDescription').value.trim(),
                is_available: document.getElementById('addProductAvailable').value
            };

            if (!payload.product_name || !payload.price) {
                alert('Product name and price are required');
                return;
            }

            fetch('create-product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Product created successfully');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Create failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error creating product');
            });
        }

        function editProduct(productId) {
            fetch('get-product.php?product_id=' + productId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editProductId').value = data.product.product_id;
                        document.getElementById('editProductName').value = data.product.product_name;
                        document.getElementById('editProductPrice').value = data.product.price;
                        document.getElementById('editProductBrand').value = data.product.brand_id || '';
                        document.getElementById('editProductCategory').value = data.product.category_id || '';
                        document.getElementById('editProductStock').value = data.product.stock;
                        document.getElementById('editProductDescription').value = data.product.description || '';
                        document.getElementById('editProductAvailable').value = data.product.is_available;
                        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                        modal.show();
                    } else {
                        alert('Failed to load product: ' + (data.message || 'unknown'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error loading product');
                });
        }

        function saveProduct() {
            const productId = document.getElementById('editProductId').value;
            const payload = {
                product_id: productId,
                product_name: document.getElementById('editProductName').value.trim(),
                price: parseFloat(document.getElementById('editProductPrice').value),
                brand_id: document.getElementById('editProductBrand').value || null,
                category_id: document.getElementById('editProductCategory').value || null,
                stock: parseInt(document.getElementById('editProductStock').value) || 0,
                description: document.getElementById('editProductDescription').value.trim(),
                is_available: document.getElementById('editProductAvailable').value
            };

            if (!payload.product_name || !payload.price) {
                alert('Product name and price are required');
                return;
            }

            fetch('update-product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Product updated');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error updating product');
            });
        }

        function deleteProduct(productId) {
            if (!confirm('Are you sure you want to delete this product?')) return;

            fetch('delete-product.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Product deleted');
                    location.reload();
                } else {
                    alert('Delete failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error deleting product');
            });
        }

        function editStock(productId) {
            // Fetch product details and populate modal
            fetch('get-product-stock.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('productIdInput').value = data.product_id;
                        document.getElementById('productNameInput').value = data.product_name;
                        document.getElementById('currentStockInput').value = data.stock;
                        document.getElementById('newStockInput').value = '';
                        document.getElementById('newStockInput').dataset.productId = productId;
                        const modal = new bootstrap.Modal(document.getElementById('updateStockModal'));
                        modal.show();
                    } else {
                        alert('Error loading product details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading product details');
                });
        }

        function saveStock() {
            const productId = document.getElementById('newStockInput').dataset.productId;
            const newStock = document.getElementById('newStockInput').value;

            if (!newStock || newStock < 0) {
                alert('Please enter a valid stock quantity');
                return;
            }

            fetch('update-stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    stock: newStock
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Stock updated successfully');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('updateStockModal'));
                    modal.hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating stock');
            });
        }

        // ---- Orders Management ----
        function viewOrder(orderId) {
            fetch('get-order.php?order_id=' + orderId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const o = data.order;
                        document.getElementById('viewOrderId').innerText = o.order_id;
                        document.getElementById('viewOrderCustomer').innerText = o.customer_name || (o.first_name + ' ' + o.last_name);
                        document.getElementById('viewOrderBranch').innerText = o.branch_name || 'N/A';
                        document.getElementById('viewOrderDate').innerText = o.order_date;
                        document.getElementById('viewOrderStatus').innerText = o.status;
                        const itemsTbody = document.getElementById('viewOrderItems');
                        itemsTbody.innerHTML = '';
                        let total = 0;
                        (o.items || []).forEach(i => {
                            const row = document.createElement('tr');
                            const subtotal = (parseFloat(i.price) * parseInt(i.quantity)).toFixed(2);
                            row.innerHTML = `<td>${i.product_name}</td><td>${i.quantity}</td><td>₱${parseFloat(i.price).toFixed(2)}</td><td>₱${subtotal}</td>`;
                            itemsTbody.appendChild(row);
                            total += parseFloat(subtotal);
                        });
                        document.getElementById('viewOrderTotal').innerText = total.toFixed(2);
                        const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                        modal.show();
                    } else {
                        alert('Failed to load order: ' + (data.message || 'unknown'));
                    }
                })
                .catch(err => { console.error(err); alert('Error loading order'); });
        }

        function editOrder(orderId) {
            fetch('get-order.php?order_id=' + orderId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editOrderId').value = data.order.order_id;
                        document.getElementById('editOrderStatus').value = data.order.status;
                        const modal = new bootstrap.Modal(document.getElementById('editOrderModal'));
                        modal.show();
                    } else {
                        alert('Failed to load order for edit');
                    }
                }).catch(err => { console.error(err); alert('Error loading order'); });
        }

        function saveOrder() {
            const orderId = document.getElementById('editOrderId').value;
            const status = document.getElementById('editOrderStatus').value;
            fetch('update-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Order updated');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editOrderModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error updating order'); });
        }

        function deleteOrder(orderId) {
            if (!confirm('Delete this order? This will restore product stock.')) return;
            fetch('delete-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Order deleted');
                    location.reload();
                } else {
                    alert('Delete failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error deleting order'); });
        }

        function openAddOrderModal() {
            // reset any existing item rows
            const container = document.getElementById('orderItemsContainer');
            if (!container) return alert('Create Order UI not available');
            const firstRow = container.querySelector('.order-item-row');
            // clear extras
            container.querySelectorAll('.order-item-row').forEach((r, idx) => { if (idx>0) r.remove(); });
            // reset first row values
            const sel = firstRow.querySelector('.item-product');
            const qty = firstRow.querySelector('.item-qty');
            if (sel) sel.value = '';
            if (qty) qty.value = 1;
            const modal = new bootstrap.Modal(document.getElementById('addOrderModal'));
            modal.show();
        }

        function addItemRow() {
            const container = document.getElementById('orderItemsContainer');
            const template = container.querySelector('.order-item-row');
            const clone = template.cloneNode(true);
            // clear values
            clone.querySelector('.item-product').value = '';
            clone.querySelector('.item-qty').value = 1;
            container.appendChild(clone);
        }

        function removeItemRow(btn) {
            const row = btn.closest('.order-item-row');
            if (!row) return;
            const container = document.getElementById('orderItemsContainer');
            // do not remove last row
            if (container.querySelectorAll('.order-item-row').length === 1) {
                row.querySelector('.item-product').value = '';
                row.querySelector('.item-qty').value = 1;
                return;
            }
            row.remove();
        }

        function createOrder() {
            const userId = document.getElementById('createOrderUser').value;
            const branchId = document.getElementById('createOrderBranch').value || null;
            const items = [];
            document.querySelectorAll('#orderItemsContainer .order-item-row').forEach(r => {
                const pid = r.querySelector('.item-product').value;
                const qty = r.querySelector('.item-qty').value;
                if (pid) items.push({ product_id: pid, quantity: qty });
            });
            if (items.length === 0) { alert('Add at least one item'); return; }

            fetch('create-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId, branch_id: branchId, items: items })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Order created (ID: ' + data.order_id + ')');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addOrderModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Create failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error creating order'); });
        }

        // ---- Payments Management (CRUD) ----
        function openAddPaymentModal() {
            document.getElementById('addPaymentOrder').value = '';
            document.getElementById('addPaymentAmount').value = '';
            document.getElementById('addPaymentMethod').value = '';
            document.getElementById('addPaymentStatus').value = 'Pending';
            const modal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
            modal.show();
        }

        function createPayment() {
            const order_id = document.getElementById('addPaymentOrder').value;
            const amount = parseFloat(document.getElementById('addPaymentAmount').value);
            const payment_method_id = document.getElementById('addPaymentMethod').value;
            const status = document.getElementById('addPaymentStatus').value;
            if (!order_id || !amount || !payment_method_id) { alert('Order, amount and payment method are required'); return; }

            fetch('create-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: order_id, amount: amount, payment_method_id: payment_method_id, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Payment created (ID: ' + data.payment_id + ')');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addPaymentModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Create failed: ' + (data.message || 'unknown'));
                }
            }).catch(err => { console.error(err); alert('Error creating payment'); });
        }

        function editPayment(paymentId) {
            fetch('get-payment.php?payment_id=' + paymentId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const p = data.payment;
                        document.getElementById('editPaymentId').value = p.payment_id;
                        document.getElementById('editPaymentAmount').value = p.amount;
                        document.getElementById('editPaymentMethod').value = p.payment_method_id;
                        document.getElementById('editPaymentStatus').value = p.status;
                        const modal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
                        modal.show();
                    } else {
                        alert('Failed to load payment: ' + (data.message || 'unknown'));
                    }
                }).catch(err => { console.error(err); alert('Error loading payment'); });
        }

        function savePayment() {
            const payment_id = document.getElementById('editPaymentId').value;
            const amount = parseFloat(document.getElementById('editPaymentAmount').value);
            const payment_method_id = document.getElementById('editPaymentMethod').value;
            const status = document.getElementById('editPaymentStatus').value;
            fetch('update-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payment_id: payment_id, amount: amount, payment_method_id: payment_method_id, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Payment updated');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editPaymentModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'unknown'));
                }
            }).catch(err => { console.error(err); alert('Error updating payment'); });
        }

        function deletePayment(paymentId) {
            if (!confirm('Delete this payment?')) return;
            fetch('delete-payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payment_id: paymentId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Payment deleted');
                    location.reload();
                } else {
                    alert('Delete failed: ' + (data.message || 'unknown'));
                }
            }).catch(err => { console.error(err); alert('Error deleting payment'); });
        }

        // ---- Brands Management ----
        function openAddBrandModal() {
            document.getElementById('addBrandName').value = '';
            document.getElementById('addBrandCountry').value = '';
            const modal = new bootstrap.Modal(document.getElementById('addBrandModal'));
            modal.show();
        }

        function createBrand() {
            const name = document.getElementById('addBrandName').value.trim();
            const country = document.getElementById('addBrandCountry').value.trim();
            if (!name) { alert('Please enter a brand name'); return; }

            fetch('create-brand.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_name: name, country: country })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Brand created');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addBrandModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Create failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error creating brand'); });
        }

        function editBrand(brandId) {
            fetch('get-brands.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const b = (data.brands || []).find(x => parseInt(x.brand_id) === parseInt(brandId));
                        if (!b) return alert('Brand not found');
                        document.getElementById('editBrandId').value = b.brand_id;
                        document.getElementById('editBrandName').value = b.brand_name;
                        document.getElementById('editBrandCountry').value = b.country || '';
                        const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
                        modal.show();
                    } else {
                        alert('Failed to load brands');
                    }
                }).catch(err => { console.error(err); alert('Error loading brands'); });
        }

        function saveBrand() {
            const id = document.getElementById('editBrandId').value;
            const name = document.getElementById('editBrandName').value.trim();
            const country = document.getElementById('editBrandCountry').value.trim();
            if (!name) return alert('Please enter a brand name');

            fetch('update-brand.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_id: id, brand_name: name, country: country })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Brand updated');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editBrandModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error updating brand'); });
        }

        function deleteBrand(brandId) {
            if (!confirm('Delete this brand?')) return;
            fetch('delete-brand.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_id: brandId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Brand deleted');
                    location.reload();
                } else {
                    alert('Delete failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error deleting brand'); });
        }

        // ---- Categories Management ----
        function openAddCategoryModal() {
            document.getElementById('addCategoryName').value = '';
            const modal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
            modal.show();
        }

        function createCategory() {
            const name = document.getElementById('addCategoryName').value.trim();
            if (!name) { alert('Please enter a category name'); return; }

            fetch('create-category.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_name: name })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Category created');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Create failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error creating category'); });
        }

        function editCategory(categoryId) {
            fetch('get-categories.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const c = (data.categories || []).find(x => parseInt(x.category_id) === parseInt(categoryId));
                        if (!c) return alert('Category not found');
                        document.getElementById('editCategoryId').value = c.category_id;
                        document.getElementById('editCategoryName').value = c.category_name;
                        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                        modal.show();
                    } else {
                        alert('Failed to load categories');
                    }
                }).catch(err => { console.error(err); alert('Error loading categories'); });
        }

        function saveCategory() {
            const id = document.getElementById('editCategoryId').value;
            const name = document.getElementById('editCategoryName').value.trim();
            if (!name) return alert('Please enter a category name');

            fetch('update-category.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: id, category_name: name })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Category updated');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert('Update failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error updating category'); });
        }

        function deleteCategory(categoryId) {
            if (!confirm('Delete this category?')) return;
            fetch('delete-category.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_id: categoryId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Category deleted');
                    location.reload();
                } else {
                    alert('Delete failed: ' + (data.message || 'unknown'));
                }
            })
            .catch(err => { console.error(err); alert('Error deleting category'); });
        }
    </script>
</body>
</html>
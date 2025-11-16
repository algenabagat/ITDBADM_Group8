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
                                    <th>Last Updated</th>
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
                                    <td><?php echo $item['date_added']; ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-edit" onclick="editStock(<?php echo $item['product_id']; ?>)">Update</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Add more sections for Orders, Payments, Brands, Categories following the same pattern -->
                
            </main>
        </div>
    </div>

    <!-- Modals would go here for Add/Edit functionality -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            // Implementation for opening modals
            console.log('Opening modal:', modalType);
        }

        function editUser(userId) {
            // Implementation for editing user
            console.log('Editing user:', userId);
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Implementation for deleting user
                console.log('Deleting user:', userId);
            }
        }

        function editProduct(productId) {
            // Implementation for editing product
            console.log('Editing product:', productId);
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                // Implementation for deleting product
                console.log('Deleting product:', productId);
            }
        }

        function editStock(productId) {
            // Implementation for editing stock
            console.log('Updating stock for product:', productId);
        }
    </script>
</body>
</html>
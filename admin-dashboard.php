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

    <!-- Modals would go here for Add/Edit functionality -->
    
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
    </script>
</body>
</html>
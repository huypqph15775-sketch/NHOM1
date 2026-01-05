<?php
session_start();
require_once 'includes/database.php';

$errors = [];
$success = [];
$stats = [];

echo "<!DOCTYPE html>";
echo "<html lang='vi'>";
echo "<head>";
echo "  <meta charset='UTF-8'>";
echo "  <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "  <title>Verify Sample Data</title>";
echo "  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "  <style>";
echo "    body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }";
echo "    .container { background: white; border-radius: 10px; padding: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }";
echo "    .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; padding: 20px; margin: 10px 0; }";
echo "    .stat-number { font-size: 36px; font-weight: bold; }";
echo "    .stat-label { font-size: 14px; opacity: 0.8; }";
echo "    .error-box { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #f5c6cb; }";
echo "    .success-box { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #c3e6cb; }";
echo "    .info-box { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #bee5eb; }";
echo "    table { margin-top: 20px; }";
echo "    thead { background: #667eea; color: white; }";
echo "    .badge-success { background-color: #28a745; }";
echo "    .badge-danger { background-color: #dc3545; }";
echo "    .badge-warning { background-color: #ffc107; color: #333; }";
echo "  </style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1 class='mb-4'><i class='fas fa-database'></i> Verify Sample Data</h1>";
echo "<hr>";

// 1. Check connection
if ($conn->connect_error) {
    $errors[] = "Database connection error: " . $conn->connect_error;
} else {
    $success[] = "✅ Database connection OK";
}

// 2. Check Admin
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM admin");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['admin_count'] = $row['count'];
        if ($row['count'] >= 3) {
            $success[] = "✅ Admin data OK ({$row['count']} accounts)";
        } else {
            $errors[] = "❌ Admin data incomplete (Found: {$row['count']}, Expected: 3)";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error checking admin: " . $e->getMessage();
}

// 3. Check Customers
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM customer");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['customer_count'] = $row['count'];
        if ($row['count'] >= 10) {
            $success[] = "✅ Customer data OK ({$row['count']} customers)";
        } else {
            $errors[] = "❌ Customer data incomplete (Found: {$row['count']}, Expected: 10)";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error checking customers: " . $e->getMessage();
}

// 4. Check Addresses
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM customer_addresses");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['address_count'] = $row['count'];
        if ($row['count'] >= 12) {
            $success[] = "✅ Address data OK ({$row['count']} addresses)";
        } else {
            $errors[] = "⚠️ Address data incomplete (Found: {$row['count']}, Expected: 12)";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error checking addresses: " . $e->getMessage();
}

// 5. Check Orders
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM customer_orders");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['order_count'] = $row['count'];
        if ($row['count'] >= 22) {
            $success[] = "✅ Order data OK ({$row['count']} orders)";
        } else {
            $errors[] = "⚠️ Order data incomplete (Found: {$row['count']}, Expected: 22)";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error checking orders: " . $e->getMessage();
}

// 6. Check Order Products
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM customer_order_products");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['order_product_count'] = $row['count'];
        if ($row['count'] >= 26) {
            $success[] = "✅ Order products OK ({$row['count']} items)";
        } else {
            $errors[] = "⚠️ Order products incomplete (Found: {$row['count']}, Expected: 26)";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error checking order products: " . $e->getMessage();
}

// 7. Check Comments
try {
    $result = $conn->query("SELECT COUNT(*) as count FROM comments");
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['comment_count'] = $row['count'];
        if ($row['count'] >= 10) {
            $success[] = "✅ Comment data OK ({$row['count']} comments)";
        } else {
            $errors[] = "⚠️ Comment data incomplete (Found: {$row['count']}, Expected: 10)";
        }
    }
} catch (Exception $e) {
    $errors[] = "Error checking comments: " . $e->getMessage();
}

// Display Results
echo "<div class='row'>";

// Status Cards
foreach ($success as $msg) {
    echo "<div class='col-12'><div class='success-box'>$msg</div></div>";
}

foreach ($errors as $msg) {
    echo "<div class='col-12'><div class='error-box'>$msg</div></div>";
}

echo "</div>";

echo "<hr>";
echo "<h3>📊 Statistics</h3>";

echo "<div class='row'>";
if (!empty($stats['admin_count'])) {
    echo "<div class='col-md-3'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . $stats['admin_count'] . "</div>";
    echo "<div class='stat-label'>Admin Accounts</div>";
    echo "</div>";
    echo "</div>";
}

if (!empty($stats['customer_count'])) {
    echo "<div class='col-md-3'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . $stats['customer_count'] . "</div>";
    echo "<div class='stat-label'>Customers</div>";
    echo "</div>";
    echo "</div>";
}

if (!empty($stats['order_count'])) {
    echo "<div class='col-md-3'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . $stats['order_count'] . "</div>";
    echo "<div class='stat-label'>Orders</div>";
    echo "</div>";
    echo "</div>";
}

if (!empty($stats['comment_count'])) {
    echo "<div class='col-md-3'>";
    echo "<div class='stat-card'>";
    echo "<div class='stat-number'>" . $stats['comment_count'] . "</div>";
    echo "<div class='stat-label'>Comments</div>";
    echo "</div>";
    echo "</div>";
}

echo "</div>";

// Test Login
echo "<hr>";
echo "<h3>🔐 Test Login</h3>";

echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<div class='info-box'>";
echo "<strong>Admin Account</strong><br>";
echo "Username: <code>admin</code><br>";
echo "Password: <code>password123</code><br>";
echo "<a href='signin.php' class='btn btn-primary btn-sm mt-2'>Try Login →</a>";
echo "</div>";
echo "</div>";

echo "<div class='col-md-6'>";
echo "<div class='info-box'>";
echo "<strong>Customer Account</strong><br>";
echo "Username: <code>ninh_nguyen</code><br>";
echo "Password: <code>password123</code><br>";
echo "<a href='signin.php' class='btn btn-primary btn-sm mt-2'>Try Login →</a>";
echo "</div>";
echo "</div>";
echo "</div>";

// Sample Customers
echo "<hr>";
echo "<h3>👥 Sample Customers</h3>";
$result = $conn->query("SELECT customer_id, customer_name, customer_user_name, customer_email, customer_phone FROM customer ORDER BY customer_id LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table class='table table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Name</th>";
    echo "<th>Username</th>";
    echo "<th>Email</th>";
    echo "<th>Phone</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['customer_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
        echo "<td><code>" . htmlspecialchars($row['customer_user_name']) . "</code></td>";
        echo "<td>" . htmlspecialchars($row['customer_email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['customer_phone']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

// Sample Orders
echo "<hr>";
echo "<h3>📦 Sample Orders</h3>";
$result = $conn->query("SELECT co.order_id, co.customer_id, c.customer_name, co.total_price, co.status, co.order_date 
                       FROM customer_orders co 
                       JOIN customer c ON co.customer_id = c.customer_id 
                       ORDER BY co.order_date DESC LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table class='table table-striped table-hover'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Order ID</th>";
    echo "<th>Customer</th>";
    echo "<th>Total</th>";
    echo "<th>Status</th>";
    echo "<th>Date</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        $statusBadge = 'badge-danger';
        if (strpos($row['status'], 'Đã giao') !== false) $statusBadge = 'badge-success';
        elseif (strpos($row['status'], 'Đang giao') !== false) $statusBadge = 'badge-warning';
        elseif (strpos($row['status'], 'Đang chờ') !== false) $statusBadge = 'badge-info';
        
        echo "<tr>";
        echo "<td>#" . $row['order_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
        echo "<td>" . number_format($row['total_price'], 0, ',', '.') . " VNĐ</td>";
        echo "<td><span class='badge $statusBadge'>" . $row['status'] . "</span></td>";
        echo "<td>" . date('d/m/Y', strtotime($row['order_date'])) . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

echo "</div>";
echo "</body>";
echo "</html>";

$conn->close();
?>

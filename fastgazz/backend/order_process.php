<?php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include "db_connect.php";

// Check DB connection
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate POST data
    $required = ['name', 'phone', 'city', 'address', 'product', 'qty', 'notes'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            die("❌ Missing required field: $field");
        }
    }

    $name    = $_POST['name'];
    $phone   = $_POST['phone'];
    $city    = $_POST['city'];
    $address = $_POST['address'];
    $product = $_POST['product'];
    $qty     = $_POST['qty'];
    $notes   = $_POST['notes'];

    // Insert customer
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, city, address) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("❌ Prepare failed (customers): " . $conn->error);
    }
    $stmt->bind_param("ssss", $name, $phone, $city, $address);
    if (!$stmt->execute()) {
        die("❌ Execute failed (customers): " . $stmt->error);
    }
    $customer_id = $conn->insert_id;

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, product, quantity, notes) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("❌ Prepare failed (orders): " . $conn->error);
    }
    $stmt->bind_param("isis", $customer_id, $product, $qty, $notes);
    if (!$stmt->execute()) {
        die("❌ Execute failed (orders): " . $stmt->error);
    }

    echo "<h2>✅ Order received!</h2><p>Thank you, $name. We will deliver your $product soon.</p>";
} else {
    echo "❌ No POST data received.";
}
?>
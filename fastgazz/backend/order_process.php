<?php
var_dump($_POST);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input
    $name    = $_POST['name'] ?? '';
    $phone   = $_POST['phone'] ?? '';
    $sectors    = $_POST['sectors'] ?? '';
    $address = $_POST['address'] ?? '';
    $product = $_POST['product'] ?? '';
    $qty     = $_POST['qty'] ?? 1;
    $notes   = $_POST['notes'] ?? '';

    // Validate required fields
    if (!$name || !$phone || !$sectors || !$address || !$product || !$qty) {
        echo "❌ Error: All fields except notes are required.";
        exit;
    }

    // Insert customer
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, sectors, address) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "❌ Prepare failed (customers): " . $conn->error;
        exit;
    }
    $stmt->bind_param("ssss", $name, $phone, $sectors, $address);
    if (!$stmt->execute()) {
        echo "❌ Execute failed (customers): " . $stmt->error;
        exit;
    }
    $customer_id = $stmt->insert_id;
    $stmt->close();

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, product, quantity, notes) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "❌ Prepare failed (orders): " . $conn->error;
        exit;
    }
    $stmt->bind_param("isis", $customer_id, $product, $qty, $notes);
    if (!$stmt->execute()) {
        echo "❌ Execute failed (orders): " . $stmt->error;
        exit;
    }

    // Set product prices
$product_prices = [
    "Cam gas — 6,500 XAF"      => 6500,
    "Total — 6,500 XAF"        => 6500,
    "Afrique gas — 6,500 XAF"  => 6500,
    "Green oil — 6,500 XAF"    => 6500,
    "sctm — 6,500 XAF"         => 6500,
    "Bocom — 6,500 XAF"        => 6500,
    "Tradex — 6,500 XAF"       => 6500,
    "eco gas — 3,000 XAF"      => 3000
];

$price = $product_prices[$product] ?? 0;
$total = $price * intval($qty);

// Insert order (add total)
$stmt = $conn->prepare("INSERT INTO orders (customer_id, product, quantity, total, notes) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo "❌ Prepare failed (orders): " . $conn->error;
    exit;
}
$stmt->bind_param("isiss", $customer_id, $product, $qty, $total, $notes);
if (!$stmt->execute()) {
    echo "❌ Execute failed (orders): " . $stmt->error;
    exit;
}
    $stmt->close();

    echo "<h2>✅ Order received!</h2><p>Thank you, $name. We will deliver your $product soon.</p>";
} else {
    echo "❌ Error: Invalid request method.";
}
$conn->close();
?>


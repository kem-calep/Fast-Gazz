<?php
var_dump($_POST);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST['name'] ?? '';
    $email   = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (!$name || !$email || !$subject || !$message) {
        echo "❌ Error: All fields are required.";
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo "❌ Prepare failed: " . $conn->error;
        exit;
    }
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    if (!$stmt->execute()) {
        echo "❌ Execute failed: " . $stmt->error;
        exit;
    }
    $stmt->close();

    echo "<h2>✅ Message sent!</h2><p>Thank you, $name. We will get back to you soon.</p>";
} else {
    echo "❌ Error: Invalid request method.";
}
$conn->close();
?>


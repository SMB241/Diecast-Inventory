<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'inventory_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  echo json_encode(["success" => false, "message" => "Database connection failed"]);
  exit;
}

$sql = "SELECT id, brand, model, price FROM diecast_products WHERE status = 'SOLD'";
$result = $conn->query($sql);

$soldItems = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $soldItems[] = $row;
  }
}

echo json_encode([
  "success" => true,
  "soldItems" => $soldItems
]);

$conn->close();
?>

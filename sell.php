<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // password if any
$db = 'inventory_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  echo json_encode([
    "success" => false,
    "message" => "Database connection failed: " . $conn->connect_error
  ]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?? '';

  if (empty($id)) {
    echo json_encode([
      "success" => false,
      "message" => "Product ID is required"
    ]);
    exit;
  }

  $stmt = $conn->prepare("SELECT * FROM diecast_products WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    echo json_encode([
      "success" => false,
      "message" => "Product not found"
    ]);
    exit;
  }

  $product = $result->fetch_assoc();

  if (strtoupper($product['status']) === 'SOLD') {
    echo json_encode([
      "success" => false,
      "message" => "Product is already marked as SOLD"
    ]);
    exit;
  }

  $update = $conn->prepare("UPDATE diecast_products SET status = 'SOLD' WHERE id = ?");
  $update->bind_param("i", $id);

  if ($update->execute()) {
    echo json_encode([
      "success" => true,
      "message" => "Product id marked as SOLD",
      "product" => [
        "id" => $product['id'],
        "brand" => $product['brand'],
        "model" => $product['model'],
        "price" => $product['price']
      ]
    ]);
  } else {
    echo json_encode([
      "success" => false,
      "message" => "Failed to update product status"
    ]);
  }

} else {
  echo json_encode([
    "success" => false,
    "message" => "Invalid request method"
  ]);
}

$conn->close();
?>

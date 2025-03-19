<?php
$conn = new mysqli("localhost", "root", "", "inventory_db");

$id = intval($_POST['id']); // POST or GET depending on fetch
$stmt = $conn->prepare("UPDATE diecast_products SET status = 'sold' WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false]);
}

$conn->close();
?>

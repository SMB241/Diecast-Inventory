<?php
$conn = new mysqli("localhost", "root", "", "inventory_db");

$id = intval($_GET['id']);

$result = $conn->query("SELECT * FROM diecast_products WHERE id = $id");
$item = $result->fetch_assoc();

echo json_encode($item);

$conn->close();
?>

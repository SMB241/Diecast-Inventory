<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$action = $_REQUEST['action'];

switch ($action) {
  case 'list':
    $sql = "SELECT * FROM diecast_products ORDER BY id DESC";
    $result = $conn->query($sql);
    $products = [];

    while ($row = $result->fetch_assoc()) {
      $products[] = $row;
    }

    echo json_encode($products);
    break;

  case 'add':
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO diecast_products (brand, model, price, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $brand, $model, $price, $status);
    $stmt->execute();
    break;

  case 'delete':
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM diecast_products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    break;

  case 'edit':
    $id = $_POST['id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE diecast_products SET brand = ?, model = ?, price = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $brand, $model, $price, $status, $id);
    $stmt->execute();
    break;

  case 'search':
    $model = $_GET['model'];
    $stmt = $conn->prepare("SELECT * FROM diecast_products WHERE model LIKE ?");
    $search = "%$model%";
    $stmt->bind_param("s", $search);
    $stmt->execute();

    $result = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
      $products[] = $row;
    }

    echo json_encode($products);
    break;

  case 'sort':
    $status = $_GET['status'];

    $stmt = $conn->prepare("SELECT * FROM diecast_products WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();

    $result = $stmt->get_result();
    $products = [];

    while ($row = $result->fetch_assoc()) {
      $products[] = $row;
    }

    echo json_encode($products);
    break;
}

$conn->close();
?>

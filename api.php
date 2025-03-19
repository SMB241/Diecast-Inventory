<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "inventory_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$action = $_REQUEST['action'] ?? '';

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
      $brand = $_POST['brand'] ?? '';
      $model = $_POST['model'] ?? '';
      $price = $_POST['price'] ?? '';
    
      if (empty($brand) || empty($model) || empty($price)) {
        echo json_encode(["success" => false, "message" => "Missing required fields"]);
        break;
      }
    
      // Set default status to 'available'
      $status = 'available';
    
      $stmt = $conn->prepare("INSERT INTO diecast_products (brand, model, price, status) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssis", $brand, $model, $price, $status);
    
      if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Product added successfully"]);
      } else {
        echo json_encode(["success" => false, "message" => "Failed to add product"]);
      }
    
      break;
    

  case 'delete':
    $id = $_POST['id'] ?? '';

    if ($id === '') {
      echo json_encode(["success" => false, "message" => "Missing ID"]);
      break;
    }

    $stmt = $conn->prepare("DELETE FROM diecast_products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
      echo json_encode(["success" => true, "message" => "Product deleted successfully"]);
    } else {
      echo json_encode(["success" => false, "message" => "Failed to delete product"]);
    }
    break;

  case 'edit':
    $id = $_POST['id'] ?? '';
    $brand = $_POST['brand'] ?? '';
    $model = $_POST['model'] ?? '';
    $price = $_POST['price'] ?? '';
    $status = $_POST['status'] ?? '';

    if ($id === '' || $brand === '' || $model === '' || $price === '' || $status === '') {
      echo json_encode(["success" => false, "message" => "Missing required fields"]);
      break;
    }

    $stmt = $conn->prepare("UPDATE diecast_products SET brand = ?, model = ?, price = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $brand, $model, $price, $status, $id);

    if ($stmt->execute()) {
      echo json_encode(["success" => true, "message" => "Product updated successfully"]);
    } else {
      echo json_encode(["success" => false, "message" => "Failed to update product"]);
    }
    break;

  case 'search':
    $model = $_GET['model'] ?? '';

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
    $status = $_GET['status'] ?? '';

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

  default:
    echo json_encode(["success" => false, "message" => "Invalid action"]);
}

$conn->close();
?>

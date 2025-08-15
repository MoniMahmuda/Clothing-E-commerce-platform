<?php
require_once __DIR__ . '/db.php';

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(["error" => "Invalid ID"]);
  exit;
}

$stmt = $conn->prepare("SELECT image_path FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
  http_response_code(404);
  echo json_encode(["error" => "Product not found"]);
  exit;
}
$row = $res->fetch_assoc();
$stmt->close();

$stmt2 = $conn->prepare("DELETE FROM products WHERE id=?");
$stmt2->bind_param("i", $id);
$ok = $stmt2->execute();
$stmt2->close();

if ($ok) {
  $abs = dirname(__DIR__) . '/' . $row['image_path'];
  if (is_file($abs)) { @unlink($abs); }
  echo json_encode(["success" => true]);
} else {
  http_response_code(500);
  echo json_encode(["error" => "Delete failed"]);
}
$conn->close();



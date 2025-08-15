<?php
require_once __DIR__ . '/db.php';

$sql = "SELECT id, name, price, size, image_path, created_at FROM products ORDER BY id DESC";
$res = $conn->query($sql);

$rows = [];
if ($res) {
  while ($r = $res->fetch_assoc()) { $rows[] = $r; }
}
echo json_encode(["products" => $rows]);
$conn->close();



<?php
require_once __DIR__ . '/db.php';

$name  = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$size  = trim($_POST['size'] ?? '');

if ($name === '' || $price === '' || $size === '') {
  http_response_code(400);
  echo json_encode(["error" => "All fields are required"]);
  exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  http_response_code(400);
  echo json_encode(["error" => "Image is required"]);
  exit;
}

// Validate image
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
finfo_close($finfo);

if (!isset($allowed[$mime])) {
  http_response_code(400);
  echo json_encode(["error" => "Only JPG, PNG, WEBP allowed"]);
  exit;
}
if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
  http_response_code(400);
  echo json_encode(["error" => "Max size 2MB"]);
  exit;
}

$uploads = dirname(__DIR__) . '/uploads';
if (!is_dir($uploads)) { mkdir($uploads, 0775, true); }

$ext = $allowed[$mime];
$filename = bin2hex(random_bytes(8)) . '.' . $ext;
$dest = $uploads . '/' . $filename;
if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
  http_response_code(500);
  echo json_encode(["error" => "Failed to save image"]);
  exit;
}

$imagePath = 'uploads/' . $filename;

$stmt = $conn->prepare("INSERT INTO products (name, price, size, image_path) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdss", $name, $price, $size, $imagePath);
$ok = $stmt->execute();
$stmt->close();

if ($ok) echo json_encode(["success" => true, "message" => "Product uploaded"]);
else {
  @unlink($dest);
  http_response_code(500);
  echo json_encode(["error" => "DB insert failed"]);
}
$conn->close();



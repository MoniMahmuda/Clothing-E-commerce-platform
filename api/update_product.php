<?php
require_once __DIR__ . '/db.php';

$id    = intval($_POST['id'] ?? 0);
$name  = trim($_POST['name'] ?? '');
$price = trim($_POST['price'] ?? '');
$size  = trim($_POST['size'] ?? '');

if ($id <= 0 || $name === '' || $price === '' || $size === '') {
  http_response_code(400);
  echo json_encode(["error" => "Invalid input"]);
  exit;
}

// get current product to possibly delete old image
$stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$current) {
  http_response_code(404);
  echo json_encode(["error" => "Product not found"]);
  exit;
}

$newImagePath = $current['image_path'];
$replaceImage = (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK);

if ($replaceImage) {
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
    echo json_encode(["error" => "Failed to save new image"]);
    exit;
  }
  $newImagePath = 'uploads/' . $filename;

  // remove old image
  $oldAbs = dirname(__DIR__) . '/' . $current['image_path'];
  if (is_file($oldAbs)) { @unlink($oldAbs); }
}

$stmt2 = $conn->prepare("UPDATE products SET name=?, price=?, size=?, image_path=? WHERE id=?");
$stmt2->bind_param("sdssi", $name, $price, $size, $newImagePath, $id);
$ok = $stmt2->execute();
$stmt2->close();

if ($ok) echo json_encode(["success" => true, "message" => "Product updated"]);
else {
  // if DB failed and new image was saved, try to clean it
  if ($replaceImage) { @unlink($dest); }
  http_response_code(500);
  echo json_encode(["error" => "Update failed"]);
}
$conn->close();

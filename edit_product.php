<?php
require_once 'db.php';

// Initialize variables
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = "";

// Fetch product details
$product = null;
if($id > 0){
    $result = $conn->query("SELECT * FROM products WHERE id = $id LIMIT 1");
    if($result && $result->num_rows > 0){
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
    }
}

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $size = $conn->real_escape_string($_POST['size']);

    // Handle image upload
    $image_path = $product['image_path'];
    if(isset($_FILES['image']) && $_FILES['image']['size'] > 0){
        $target_dir = "uploads/";
        $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;

        if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)){
            $image_path = $target_file;
        } else {
            $message = "Image upload failed.";
        }
    }

    // Update product
    $sql = "UPDATE products SET name='$name', price=$price, size='$size', image_path='$image_path' WHERE id=$id";
    if($conn->query($sql)){
        $message = "Product updated successfully.";
        // Refresh product data
        $result = $conn->query("SELECT * FROM products WHERE id = $id LIMIT 1");
        $product = $result->fetch_assoc();
    } else {
        $message = "Error updating product: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background-color: #f7f7f7; }
h2 { margin-bottom: 20px; color: #333; }
form { background: #fff; padding: 20px; border-radius: 5px; width: 400px; }
form input, form select { width: 100%; padding: 8px; margin: 8px 0; }
form button { padding: 10px 15px; background-color: #007bff; color: white; border: none; cursor: pointer; }
form button:hover { background-color: #0056b3; }
img { max-width: 150px; margin-top: 10px; }
.message { margin-bottom: 10px; color: green; }
</style>
</head>
<body>

<h2>Edit Product</h2>
<?php if($message): ?>
<p class="message"><?= $message ?></p>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label>Price:</label>
    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>

    <label>Size:</label>
    <select name="size" required>
        <?php
        $sizes = ["N", "S", "M", "L", "XL"];
        foreach($sizes as $s){
            $selected = $s == $product['size'] ? "selected" : "";
            echo "<option value='$s' $selected>$s</option>";
        }
        ?>
    </select>

    <label>Current Image:</label>
    <br>
    <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image">

    <label>Change Image (optional):</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Update Product</button>
</form>

<br>
<button onclick="window.location.href='manage_products.php'">← Back to Manage Products</button>

</body>
</html>

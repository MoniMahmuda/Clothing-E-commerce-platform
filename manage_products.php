<?php
// manage_products.php
require_once 'db.php';

// Delete product if delete_id is provided via GET
if(isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM products WHERE id = $delete_id");
    header("Location: manage_products.php"); // Refresh page after deletion
    exit;
}

// Fetch all products
$sql = "SELECT id, supplier_id, name, price, size, image_path, created_at FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
$products = [];
if($result){
    while($row = $result->fetch_assoc()){
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Manage Products</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; background-color: #f7f7f7; }
h2 { margin-bottom: 20px; color: #333; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background-color: #007bff; color: white; }
tr:nth-child(even) { background-color: #f2f2f2; }
img { width: 80px; height: 80px; object-fit: cover; }
.btn-delete { padding: 5px 10px; background-color: #dc3545; color: white; border: none; cursor: pointer; }
.btn-delete:hover { background-color: #c82333; }
.btn-home { padding: 8px 15px; background-color: #28a745; color: white; border: none; cursor: pointer; margin-bottom: 10px; }
.btn-home:hover { background-color: #218838; }
</style>
</head>
<body>

<!-- Back Home Button -->
<button class="btn-home" onclick="window.location.href='dashboard-admin.html'">← Back to Home</button>

<h2>📦 Manage Products</h2>

<table>
<thead>
<tr>
<th>ID</th>
<th>Supplier ID</th>
<th>Name</th>
<th>Price (Tk)</th>
<th>Size</th>
<th>Image</th>
<th>Created At</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php if(count($products) > 0): ?>
    <?php foreach($products as $prod): ?>
        <tr>
            <td><?= $prod['id'] ?></td>
            <td><?= $prod['supplier_id'] ?? 'N/A' ?></td>
            <td><?= htmlspecialchars($prod['name']) ?></td>
            <td><?= number_format($prod['price'], 2) ?></td>
            <td><?= htmlspecialchars($prod['size']) ?></td>
            <td><img src="<?= htmlspecialchars($prod['image_path']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>"></td>
            <td><?= $prod['created_at'] ?></td>
            <td>
                <a href="edit_product.php?id=<?= $prod['id'] ?>"><button>Edit</button></a>
                <a href="manage_products.php?delete_id=<?= $prod['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">
                    <button class="btn-delete">Delete</button>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8">No products found.</td>
    </tr>
<?php endif; ?>
</tbody>
</table>

</body>
</html>

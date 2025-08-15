<?php
// admin_order.php
require_once 'db.php';

// Delete order if 'delete_id' is provided via GET
if(isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM orders WHERE id = $delete_id");
    header("Location: admin_order.php"); // Refresh page after deletion
    exit;
}

// Fetch all orders
$sql = "SELECT id, customer_name, customer_email, product_name, size, quantity, price, (quantity*price) AS total, order_date 
        FROM orders 
        ORDER BY order_date DESC";

$result = $conn->query($sql);
$orders = [];
if ($result) {
    while($row = $result->fetch_assoc()){
        $orders[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - All Orders</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background-color: burlywood; }
    h2 { margin-bottom: 20px; color: #333; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #007bff; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .btn-delete { padding: 5px 10px; background-color: #dc3545; color: white; border: none; cursor: pointer; }
    .btn-delete:hover { background-color: #c82333; }
    .btn-home { padding: 8px 15px; background-color: #28a745; color: white; border: none; cursor: pointer; margin-bottom: 10px; }
    .btn-home:hover { background-color: #218838; }
    a { text-decoration: none; color: #007bff; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <!-- Back Home Button -->
  <button class="btn-home" onclick="window.location.href='dashboard-admin.html'">← Back to Home</button>

  <h2>📦 All Orders</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Customer Name</th>
        <th>Email</th>
        <th>Product</th>
        <th>Size</th>
        <th>Quantity</th>
        <th>Price (Tk)</th>
        <th>Total (Tk)</th>
        <th>Order Date</th>
        <th>Action</th> <!-- New Action column -->
      </tr>
    </thead>
    <tbody>
      <?php if(count($orders) > 0): ?>
        <?php foreach($orders as $order): ?>
          <tr>
            <td><?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['customer_name']) ?></td>
            <td><?= htmlspecialchars($order['customer_email']) ?></td>
            <td><?= htmlspecialchars($order['product_name']) ?></td>
            <td><?= htmlspecialchars($order['size']) ?></td>
            <td><?= $order['quantity'] ?></td>
            <td><?= number_format($order['price'], 2) ?></td>
            <td><?= number_format($order['total'], 2) ?></td>
            <td><?= $order['order_date'] ?></td>
            <td>
              <a href="admin_order.php?delete_id=<?= $order['id'] ?>" onclick="return confirm('Are you sure you want to delete this order?');">
                <button class="btn-delete">Delete</button>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="10">No orders found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>


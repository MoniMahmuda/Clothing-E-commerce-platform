<?php
require_once 'db.php';
$sql = "SELECT * FROM user_t WHERE role='customer'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Customers</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
<h2>Customers</h2>
<table class="table table-bordered">
<thead>
<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= $row['name'] ?></td>
<td><?= $row['email'] ?></td>
<td><a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>

<?php
require_once 'db.php';
if(!isset($_POST['id'])) die('Invalid request');

$id = intval($_POST['id']);
$stmt = $conn->prepare("DELETE FROM orders WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    header("Location: admin_orders.php");
} else {
    echo "Error deleting order: ".$conn->error;
}

$stmt->close();
$conn->close();
?>

<?php
header('Content-Type: application/json');
require_once 'db.php';

$sql = "SELECT id, customer_name, customer_email, product_name, size, quantity, price,
               (quantity*price) AS total, order_date
        FROM orders
        ORDER BY order_date DESC";

$result = $conn->query($sql);
if(!$result){
    echo json_encode(['success'=>false,'message'=>$conn->error]);
    $conn->close();
    exit;
}

$orders = [];
while($row = $result->fetch_assoc()){
    $orders[] = $row;
}

echo json_encode(['success'=>true,'orders'=>$orders]);
$conn->close();
?>

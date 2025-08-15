<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

// Check session user (for demo, set dummy if not logged in)
if(!isset($_SESSION['user_name'])) $_SESSION['user_name'] = 'Test User';
if(!isset($_SESSION['user_email'])) $_SESSION['user_email'] = 'test@example.com';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if($data === null || !isset($data['cart']) || !is_array($data['cart']) || count($data['cart']) === 0){
    echo json_encode(['success'=>false, 'message'=>'Invalid or empty cart data']);
    exit;
}

$cart = $data['cart'];
$order_date = date('Y-m-d H:i:s');
$customer_name = $_SESSION['user_name'];
$customer_email = $_SESSION['user_email'];

// Insert each cart item
foreach($cart as $item){
    if(!isset($item['name'],$item['size'],$item['qty'],$item['price'])){
        echo json_encode(['success'=>false,'message'=>'Invalid cart item data']);
        exit;
    }

    $product_name = $conn->real_escape_string($item['name']);
    $size = $conn->real_escape_string($item['size']);
    $qty = intval($item['qty']);
    $price = floatval($item['price']);

    $sql = "INSERT INTO orders (customer_name, customer_email, product_name, size, quantity, price, order_date)
            VALUES ('$customer_name','$customer_email','$product_name','$size',$qty,$price,'$order_date')";

    if(!$conn->query($sql)){
        echo json_encode(['success'=>false,'message'=>$conn->error]);
        $conn->close();
        exit;
    }
}

echo json_encode(['success'=>true,'message'=>'Order saved successfully!']);
$conn->close();
?>

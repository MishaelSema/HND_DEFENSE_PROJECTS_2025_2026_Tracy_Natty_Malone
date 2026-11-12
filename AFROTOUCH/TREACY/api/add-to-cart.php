<?php
session_start();
include 'db.php';

$session_id = session_id();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id > 0) {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    echo json_encode(['success' => true, 'cart_count' => array_sum($_SESSION['cart'])]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid product']);
}

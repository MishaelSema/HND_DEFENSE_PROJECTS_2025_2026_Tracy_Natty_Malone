<?php
session_start();

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$change = isset($_POST['change']) ? (int)$_POST['change'] : 0;

if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $change;
    
    if ($_SESSION['cart'][$product_id] <= 0) {
        unset($_SESSION['cart'][$product_id]);
    }
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

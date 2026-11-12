<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch products with seller info
$sql = "SELECT p.id, p.name AS product, p.price, p.description, s.name AS seller 
        FROM products p 
        JOIN sellers s ON p.seller_id = s.id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Home</title>
  
  <style>
    table { border-collapse: collapse; width: 80%; margin: 20px auto; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background: #f4f4f4; }
    button { padding: 6px 12px; background: #28a745; color: white; border: none; cursor: pointer; }
    button:hover { background: #218838; }
  </style>
</head>
<body>
  <h2 style="text-align:center;">Welcome Buyer</h2>
  <table>
    <tr>
      <th>Product</th>
      <th>Price</th>
      <th>Description</th>
      <th>Seller</th>
      <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['product']; ?></td>
        <td>KES <?php echo $row['price']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['seller']; ?></td>
        <td>
          <form method="POST" action="buy.php">
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <button type="submit">Buy</button>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>

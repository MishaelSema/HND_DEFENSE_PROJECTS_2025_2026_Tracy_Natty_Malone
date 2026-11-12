<?php
$conn = new mysqli("localhost","root","","application");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    $stmt = "SELECT * FROM applicants";
    $results = $conn->query($stmt);
    if($row = $results->fetch_assoc()){
        echo "<p>" . $row['country'] . "</p>";
    }else{
        echo "<h1> CHECK YA CODE BOI</h1>";
    };

    ?>
</body>
</html>
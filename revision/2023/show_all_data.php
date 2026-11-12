<?php $conn = new mysqli("localhost","root", "","SWE"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
   <table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Zip Code</th>
        <th>Country</th>
    </tr>
    <?php
        $results = $conn->query("SELECT * FROM students");
        while($row = $results->fetch_assoc()){
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['zipcode'] . "</td>";
            echo "<td>" . $row['country'] . "</td>";
            echo "</tr>";
        };

    ?>
   </table> 
</body>
</html>
<?php $conn->close(); ?>
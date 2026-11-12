<?php
$conn = new mysqli("localhost", "root","");

$create_db_sql = "CREATE DATABASE IF NOT EXISTS SWE;";
$conn->query($create_db_sql);

$conn->select_db("SWE");
$table = "CREATE TABLE IF NOT EXISTS students(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100),
            zipcode VARCHAR(100),
            country VARCHAR(100)
);";
$conn->query($table);

$name = $_POST['name'];
$zipcode = $_POST['zipcode'];
$email = $_POST['email'];
$country = $_POST['country'];

$stmt = $conn->prepare("INSERT INTO students(name,email,zipcode,country) VALUE(?,?,?,?);");
$stmt->bind_param("ssss",$name,$email,$zipcode,$country);

if($stmt->execute()){
    echo "<p>Database updated successfully</p>";
    echo '<p>SEE ALL DATA: <a href=show_all_data.php target="_blanc" >CLICK HERE</a> </p>';
}else{
    echo "<p>Failed to update Database</p>";
};
$stmt->close();
$conn->close();

?>
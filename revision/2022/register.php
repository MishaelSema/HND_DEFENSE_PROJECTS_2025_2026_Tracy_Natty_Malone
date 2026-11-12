<?php
echo "<p> HOE ASS NIGGA</p>";
$conn = new mysqli("localhost", "root","");
$setupDB = "CREATE DATABASE IF NOT EXISTS application;";
$conn->query($setupDB);
$conn->select_db("application");
$conn->query("CREATE TABLE IF NOT EXISTS delidded(
                id INT AUTO_INCREMENT PRIMARY KEY,
                lastname VARCHAR(50),
                course VARCHAR(50),
                gender VARCHAR(50),
                phone VARCHAR(50),
                address VARCHAR(150),
                email VARCHAR(50),
                password VARCHAR(250)
);");
$lastname = $_POST['lastname'];
$course = $_POST['course'];
$gender = $_POST['gender'];
$phone = $_POST['fullPhone'];
$address = $_POST['address'];
$email = $_POST['email'];
$password = $_POST['firstPassword'];
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO delidded(lastname,course,gender,phone,address,email,password) VALUES(?,?,?,?,?,?,?);");
$stmt->bind_param("sssssss", $lastname,$course,$gender,$phone,$address,$email,$hashed_password);
if($stmt->execute()){
    echo "<p>Database updated successfully</p>";
}else{
    echo "<p>Failed to update Database</p>";
};
$stmt->close();
$conn->close();




?>
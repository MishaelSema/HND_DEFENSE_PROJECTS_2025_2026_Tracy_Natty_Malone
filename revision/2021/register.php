<?php
$conn = new mysqli("localhost","root","");
$setupDB = "CREATE DATABASE IF NOT EXISTS application;";
$conn->query($setupDB);
$conn->select_db("application");
$createTable = "CREATE TABLE IF NOT EXISTS applicants(
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    fname VARCHAR(50),
                    email VARCHAR(100),
                    subject VARCHAR(150),
                    country VARCHAR(100),
                    comment text,
                    gender VARCHAR(20),
                    hdl VARCHAR(150)                    
                );"; 
$conn->query($createTable);

$fn = $_POST['fName'];
$email = $_POST['email'];
$subject = $_POST['subject'];
$country = $_POST['country'];
$comment = $_POST['comment'];
$gender = $_POST['gender'];
$hdl = "";
if(isset($_POST['hdl'])){
    $hdl = implode(",",$_POST['hdl']);
}

$stmt = $conn->prepare("INSERT INTO applicants(fname,email,subject,country,comment,gender,hdl) VALUES(?,?,?,?,?,?,?);");
$stmt->bind_param("sssssss",$fn,$email,$subjet,$country,$comment,$gender,$hdl);

if($stmt->execute()){
    echo "<h1>GOOD JOB DATABASE UPDATED</h1>";
    echo "<a href="showData.html" target="_blank">CLICK ME</a>";

}else{
    echo "<h1>CHECK YA CODE</h1>" ;
};

$stmt->close();
$conn->close();



?>
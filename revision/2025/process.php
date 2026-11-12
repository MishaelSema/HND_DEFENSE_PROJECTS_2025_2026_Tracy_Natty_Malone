<?php
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $conn = new mysqli("localhost", "root", "", "HND_STUDENTS");

    if($conn->connect_error){
        die("CONNECTION FAILED: ". $conn->connect_error);
    };

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $dob = DateTime::createFromFormat('d/m/Y', $_POST['dob']);
    $dob = $dob->format('Y-m-d');
    $pob = $_POST['pob'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO students(name, surname, dob, pob, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $surname, $dob, $pob, $email, $phone);

    if($stmt->execute()){
        $message = "Good Shitttt! Data Submitted Successfully";
    }else{
        $message = "Error Submitting Data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HND Student Information</title>
</head>
<body>
    <h1>HND Student Information</h1>
    <?php if($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <br>
    <form action="process.php" method="POST">
        <b>Name:</b> <input type="text" name="name" required><br><br>
        <b>Surname:</b> <input type="text" name="surname" required><br><br>
        <b>Birth Date (formatted as dd/mm/yyyy):</b> <input type="text" name="dob" placeholder="dd/mm/yyyy" required><br><br>
        <b>Place of birth:</b> <input type="text" name="pob" required><br><br>
        <b>E-mail:</b> <input type="email" name="email" required><br><br>
        <b>Phone Number:</b> <input type="tel" name="phone" required><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
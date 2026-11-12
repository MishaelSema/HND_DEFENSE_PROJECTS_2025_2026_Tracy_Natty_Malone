<?php
// Get form data
$name = $_POST['name'];
$zipcode = $_POST['zipcode'];
$email = $_POST['email'];
$country = $_POST['country'];

// Create text file and save data
$file = fopen("hnd2023.txt", "a");
$line = "$name|$zipcode|$email|$country\n";
fwrite($file, $line);
fclose($file);

// Redirect back with success message
echo "Data saved successfully! <a href='display_file.php'>View Data</a>";
?>
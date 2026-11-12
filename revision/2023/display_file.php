<!DOCTYPE html>
<html>
<head>
    <title>Form Data from File</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Student Data from File</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Zip Code</th>
            <th>Email</th>
            <th>Country</th>
        </tr>
        <?php
        if(file_exists("hnd2023.txt")) {
            $file = fopen("hnd2023.txt", "r");
            while(!feof($file)) {
                $line = fgets($file);
                if($line != "") {
                    $data = explode("|", $line);
                    echo "<tr>";
                    echo "<td>" . $data[0] . "</td>";
                    echo "<td>" . $data[1] . "</td>";
                    echo "<td>" . $data[2] . "</td>";
                    echo "<td>" . $data[3] . "</td>";
                    echo "</tr>";
                }
            }
            fclose($file);
        }
        ?>
    </table>
</body>
</html>

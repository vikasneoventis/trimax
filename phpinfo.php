<?php

//phpinfo() ;

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "trimax";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$customerGroup = array(3,4,5,6,7,8,10,11,12,13,14,15,16);
$sql = '';
for($i=2; $i < 57; $i++)
{
    foreach ($customerGroup as $_group) {
        $sql .= "INSERT INTO `mageworx_downloads_attachment_customer_group`(`attachment_id`, `customer_group_id`)
VALUES ($i,$_group);";
    }
}

echo $sql;die;
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cgs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];

$sql = "SELECT CONCAT(G.g_name, ' -- ', C.crs_name) AS Group_Course, C.crs_id,G.g_id
        FROM Groupp G
        JOIN Teach T ON G.g_id = T.g_id
        JOIN Course C ON T.crs_id = C.crs_id
        JOIN dr D ON T.dr_id = D.dr_id
        WHERE D.username = '$username'";

$result = $conn->query($sql);

if ($result) {
    $groups = [];
    while ($row = $result->fetch_assoc()) {
        $groups[] = [
            'group_course' => $row['Group_Course'],
            'crs_id' => $row['crs_id'],
            'g_id' => $row['g_id'],

        ];
    }
    echo json_encode($groups);
} else {
    echo "Query failed: " . $conn->error;
}

$conn->close();
?>

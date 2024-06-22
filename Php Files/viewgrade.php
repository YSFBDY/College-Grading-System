<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cgs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$groupCourse = $_POST['groupCourse'];
$group_id = $_POST['groupid'];

$sql = "SELECT M.std_id, M.crs_id, M.grade,S.std_name
        FROM midterm M
        JOIN Student S ON M.std_id = S.std_id
        WHERE S.g_id = '$group_id'
        AND M.crs_id = '$groupCourse'";
        

$result = $conn->query($sql);

if ($result) {
    $gradeData = [];
    while ($row = $result->fetch_assoc()) {
        $gradeData[] = $row;
    }
    echo json_encode($gradeData);
} else {
    echo "Query failed: " . $conn->error;
}

$conn->close();
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cgs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$CourseId  = $_POST['groupCourse'];
$groupId  = $_POST['groupid'];


$sql = "SELECT A.*, S.std_name
        FROM attendence A
        JOIN Student S ON A.std_id = S.std_id
        WHERE A.std_id IN (
                    SELECT S.std_id
                    FROM Student S
                    WHERE S.g_id = '$groupId'
                )
        AND A.crs_id = '$CourseId'"; // Assuming your table is named 'attendance'

$result = $conn->query($sql);

if ($result) {
    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }
    echo json_encode($attendanceData);
} else {
    echo "Query failed: " . $conn->error;
}

$conn->close();
?>
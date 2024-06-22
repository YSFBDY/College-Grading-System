<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cgs";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentId = $_POST['student_id'];  
$selectedWeek = $_POST['week'];
$CourseId  = $_POST['groupCourse'];
$groupId = $_POST['groupid'];

// Assume your table is named 'attendance'
$tableName = 'attendence';


if($studentId!=""){


// Check if the student ID exists in the table
$checkStudentQuery = "SELECT COUNT(*) as count FROM $tableName WHERE std_id = '$studentId'";

$checkStudentResult = $conn->query($checkStudentQuery);

$checkSection = "SELECT * FROM attendence A 
WHERE A.std_id = '$studentId' 
AND A.std_id IN (SELECT S.std_id FROM Student S WHERE S.g_id = '$groupId')
AND A.crs_id = '$CourseId';
";

$checkSectionResult = $conn->query($checkSection);

$rowCount_S = mysqli_num_rows($checkSectionResult);	




if ($checkStudentResult) {

    if ($rowCount_S > 0) {

        $rowCount = $checkStudentResult->fetch_assoc()['count'];

        if ($rowCount > 0) {
            // Student ID exists, update the corresponding week column
            $updateQuery = "UPDATE $tableName SET $selectedWeek = 1 WHERE std_id = '$studentId' AND crs_id = '$CourseId'";
            $updateResult = $conn->query($updateQuery);

            if ($updateResult) {
                echo json_encode(["status" => "success", "message" => "Attendance submitted successfully"]);

            } else {
                echo json_encode(["status" => "error", "message" => "Failed to submit attendance: " . $conn->error]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Student ID does not exist, attendance not submitted."]);

        }
    } else {
        echo json_encode(["status" => "no", "message" => "Failed to check student ID: " . $conn->error]);
    }


}else {
        echo json_encode(["status" => "error", "message" => "Failed to check student ID: " . $conn->error]);
    }

$conn->close();
}else {
    echo json_encode(["status" => "none", "message" => "Failed to check student ID: " . $conn->error]);
}
?>
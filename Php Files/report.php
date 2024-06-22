<?php
// Include PhpSpreadsheet classes
require_once __DIR__ . '/../PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $CourseId = $_POST['groupCourse'];
    $groupId = $_POST['groupid'];

    // Your SQL query
    $sql = "SELECT S.std_id as Student_ID, S.std_name as Student_Name, M.crs_id as Course_Id, C.crs_name as Course_Name,
    M.grade as Mid_Grade, 
    A1.w1 as W1_Att, A1.w2 as W2_Att, A1.w3 as W3_Att, A1.w4 as W4_Att, A1.w5 as W5_Att, A1.w6 as W6_Att, A1.w7 as W7_Att, A1.w8 as W8_Att, A1.w9 as W9_Att, A1.w10 as W10_Att, A1.w11 as W11_Att, A1.w12 as W12_Att, A1.w13 as W13_Att, A1.w14 as W14_Att, A1.w15 as W15_Att, A1.TotalAttended,
    A2.w1 as W1_Ass, A2.w2 as W2_Ass, A2.w3 as W3_Ass, A2.w4 as W4_Ass, A2.w5 as W5_Ass, A2.w6 as W6_Ass, A2.w7 as W7_Ass, A2.w8 as W8_Ass, A2.w9 as W9_Ass, A2.w10 as W10_Ass, A2.w11 as W11_Ass, A2.w12 as W12_Ass, A2.w13 as W13_Ass, A2.w14 as W14_Ass, A2.w15 as W15_Ass, A2.TotalSubmitted
    FROM Student S
    LEFT JOIN midterm M ON S.std_id = M.std_id AND M.crs_id = '$CourseId'
    LEFT JOIN attendence A1 ON S.std_id = A1.std_id AND A1.crs_id = '$CourseId'
    LEFT JOIN assignment A2 ON S.std_id = A2.std_id AND A2.crs_id = '$CourseId'
    LEFT JOIN Course C ON C.crs_id = '$CourseId'
    WHERE S.g_id = '$groupId'";

    // Replace the database connection details with your own
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cgs";

    // Execute the query
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $result = $conn->query($sql);

    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set the sheet title to CourseId + GroupId
    $sheetTitle = $CourseId . '_' . $groupId;
    $sheet->setTitle($sheetTitle);

    // Write headers
    $columnIndex = 1;
    $headers = [
        'Student_ID', 'Student_Name', 'Course_Id', 'Course_Name', 'Mid_Grade',
        'W1_Att', 'W2_Att', 'W3_Att', 'W4_Att', 'W5_Att', 'W6_Att', 'W7_Att', 'W8_Att', 'W9_Att', 'W10_Att', 
        'W11_Att', 'W12_Att', 'W13_Att', 'W14_Att', 'W15_Att', 'TotalAttended', 
        'W1_Ass', 'W2_Ass', 'W3_Ass', 'W4_Ass', 'W5_Ass', 'W6_Ass', 'W7_Ass', 'W8_Ass', 'W9_Ass', 'W10_Ass', 
        'W11_Ass', 'W12_Ass', 'W13_Ass', 'W14_Ass', 'W15_Ass', 'TotalSubmitted'
    ];

    foreach ($headers as $header) {
        $cell = Coordinate::stringFromColumnIndex($columnIndex) . '1';
        $sheet->setCellValue($cell, $header);

        // Apply styles to header cells
        $sheet->getStyle($cell)->getFont()->setBold(true);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFADD8E6');

        $columnIndex++;
    }

    // Write data
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $columnIndex = 1;
        foreach ($row as $value) {
            $cell = Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex;
            $sheet->setCellValue($cell, $value);

            // Apply border to data cells
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Set alignment for data cells
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $columnIndex++;
        }
        $rowIndex++;
    }

    // Auto size columns for each column
    foreach (range(1, $columnIndex - 1) as $col) {
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
    }

    // Save the spreadsheet as an Excel file
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $fileName = 'Report_' . $CourseId . '_Group-' . $groupId . '.xlsx';
    $writer->save($fileName);

    // Close the database connection
    $conn->close();

    // Output download link
    echo "Spreadsheet has been created. <a href='$fileName'>Download it here</a>.";
}
?>

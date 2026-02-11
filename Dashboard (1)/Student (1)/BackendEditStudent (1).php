<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $student_id = $_SESSION['edit_id'] ?? null;
    $studid = trim($input['studid'] ?? '');
    $studfirstname = trim($input['studfirstname'] ?? '');
    $studmidname = trim($input['studmidname'] ?? '');
    $studlastname = trim($input['studlastname'] ?? '');
    $studcollid = $input['studcollid'] ?? '';
    $studprogid = $input['studprogid'] ?? '';
    $studyear = $input['studyear'] ?? '';
    $errors = [];

    if (!$student_id) {
        echo json_encode(["success" => false, "errors" => ["Invalid student ID."]]);
        exit();
    }

    if (strlen($studfirstname) > 100) {
        $errors[] = "First name cannot exceed 100 characters.";
    }
    if (strlen($studlastname) > 100) {
        $errors[] = "Last name cannot exceed 100 characters.";
    }
    if (empty($studcollid)) {
        $errors[] = "College ID is required.";
    }
    if (empty($studprogid)) {
        $errors[] = "Program ID is required.";
    }
    if (empty($studyear) || !is_numeric($studyear) || $studyear < 1 || $studyear > 5) {
        $errors[] = "Year must be a number between 1 and 5.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("UPDATE students 
                                   SET studid = :studid, 
                                       studfirstname = :studfirstname, 
                                       studmidname = :studmidname, 
                                       studlastname = :studlastname, 
                                       studcollid = :studcollid, 
                                       studprogid = :studprogid, 
                                       studyear = :studyear 
                                   WHERE studid = :student_id");
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':studid', $studid);
            $stmt->bindParam(':studfirstname', $studfirstname);
            $stmt->bindParam(':studmidname', $studmidname);
            $stmt->bindParam(':studlastname', $studlastname);
            $stmt->bindParam(':studcollid', $studcollid);
            $stmt->bindParam(':studprogid', $studprogid);
            $stmt->bindParam(':studyear', $studyear);
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            $errors[] = "Error updating student: " . $e->getMessage();
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }
}

// Endpoint to fetch student details
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $query = $dbh->prepare("SELECT * FROM students WHERE studid = :student_id");
    $query->bindParam(':student_id', $student_id);
    $query->execute();
    $student = $query->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        echo json_encode($student);
    } else {
        echo json_encode(["success" => false, "errors" => ["Student not found."]]);
    }
}
?>

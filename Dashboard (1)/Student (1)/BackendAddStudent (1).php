<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = file_get_contents('php://input'); // Read raw POST data
    $data = json_decode($input, true); // Decode JSON to associative array

    // Validate the data
    $studid = trim($data['studid'] ?? '');
    $studfirstname = trim($data['studfirstname'] ?? '');
    $studmidname = trim($data['studmidname'] ?? '');
    $studlastname = trim($data['studlastname'] ?? '');
    $studcollid = $data['studcollid'] ?? '';
    $studprogid = $data['studprogid'] ?? '';
    $studyear = $data['studyear'] ?? '';

    if (empty($studid)) {
        $errors[] = "Student ID is required.";
    }
    if (empty($studfirstname)) {
        $errors[] = "First name is required.";
    }
    if (empty($studlastname)) {
        $errors[] = "Last name is required.";
    }
    if (empty($studcollid)) {
        $errors[] = "Please select a college.";
    }
    if (empty($studprogid)) {
        $errors[] = "Please select a program.";
    }
    if (empty($studyear) || !is_numeric($studyear) || $studyear < 1 || $studyear > 5) {
        $errors[] = "Year must be a number between 1 and 5.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("INSERT INTO students (studid, studfirstname, studmidname, studlastname, studcollid, studprogid, studyear) 
                                   VALUES (:studid, :studfirstname, :studmidname, :studlastname, :studcollid, :studprogid, :studyear)");
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
            $errors[] = "Database error: " . $e->getMessage();
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }

    exit();
}

// Fetch colleges for dropdown
if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_GET['collegeId'])) {
    $collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
    $colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($colleges);
    exit();
}

// Fetch programs for a college
if (isset($_GET['collegeId'])) {
    $collegeId = $_GET['collegeId'];

    $programsQuery = $dbh->prepare("SELECT progid, progfullname FROM programs WHERE progcollid = :collegeId");
    $programsQuery->bindParam(':collegeId', $collegeId);
    $programsQuery->execute();

    $programs = $programsQuery->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($programs);
    exit();
}
?>

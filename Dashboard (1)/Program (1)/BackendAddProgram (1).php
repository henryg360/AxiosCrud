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
    $program_id = trim($data['program_id'] ?? '');
    $program_name = trim($data['program_name'] ?? '');
    $program_shortname = trim($data['program_shortname'] ?? '');
    $college_id = $data['college_id'] ?? '';
    $department_id = $data['department_id'] ?? '';

    if (empty($program_id)) {
        $errors[] = "Program ID is required.";
    } elseif (!ctype_digit($program_id) || strlen($program_id) > 11) {
        $errors[] = "Program ID must be numeric and not exceed 11 digits.";
    }
    if (strlen($program_name) > 100) {
        $errors[] = "Program name cannot exceed 100 characters.";
    }
    if (strlen($program_shortname) > 20) {
        $errors[] = "Program short name cannot exceed 20 characters.";
    }
    if (empty($college_id)) {
        $errors[] = "Please select a college.";
    }
    if (empty($department_id)) {
        $errors[] = "Please select a department.";
    }

    // Check for duplicate Program ID
    $duplicateCheckQuery = $dbh->prepare("SELECT COUNT(*) AS count FROM programs WHERE progid = :progid");
    $duplicateCheckQuery->bindParam(':progid', $program_id);
    $duplicateCheckQuery->execute();
    $duplicateCheckResult = $duplicateCheckQuery->fetch(PDO::FETCH_ASSOC);

    if ($duplicateCheckResult['count'] > 0) {
        $errors[] = "Program ID must be unique.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("INSERT INTO programs (progid, progfullname, progshortname, progcollid, progcolldeptid) 
                                   VALUES (:progid, :progfullname, :progshortname, :progcollid, :progcolldeptid)");
            $stmt->bindParam(':progid', $program_id);
            $stmt->bindParam(':progfullname', $program_name);
            $stmt->bindParam(':progshortname', $program_shortname);
            $stmt->bindParam(':progcollid', $college_id);
            $stmt->bindParam(':progcolldeptid', $department_id);

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

// Endpoint to fetch colleges for the dropdown
if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_GET['college_id']) && !isset($_GET['program_id_check'])) {
    $collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
    $colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($colleges);
    exit();
}

// Endpoint to fetch departments based on the selected college
if (isset($_GET['college_id'])) {
    $college_id = $_GET['college_id'];

    // Fetch departments for the selected college
    $departmentsQuery = $dbh->prepare("SELECT deptid, deptfullname FROM departments WHERE deptcollid = :college_id");
    $departmentsQuery->bindParam(':college_id', $college_id);
    $departmentsQuery->execute();

    $departments = $departmentsQuery->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($departments);
    exit();
}

// Endpoint to check for duplicate Program ID
if (isset($_GET['program_id_check'])) {
    $program_id = $_GET['program_id_check'];

    // Check if Program ID exists
    $duplicateCheckQuery = $dbh->prepare("SELECT COUNT(*) AS count FROM programs WHERE progid = :progid");
    $duplicateCheckQuery->bindParam(':progid', $program_id);
    $duplicateCheckQuery->execute();
    $duplicateCheckResult = $duplicateCheckQuery->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['exists' => $duplicateCheckResult['count'] > 0]);
    exit();
}
?>

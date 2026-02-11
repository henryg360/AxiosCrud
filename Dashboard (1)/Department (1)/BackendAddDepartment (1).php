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
    $dept_id = trim($data['dept_id'] ?? '');
    $full_name = trim($data['full_name'] ?? '');
    $short_name = trim($data['short_name'] ?? '');
    $college_id = $data['college_id'] ?? '';

    if (empty($dept_id)) {
        $errors[] = "Department ID is required.";
    } elseif (!ctype_digit($dept_id) || strlen($dept_id) > 11) {
        $errors[] = "Department ID must be numeric and not exceed 11 digits.";
    }
    if (strlen($full_name) > 100) {
        $errors[] = "Full name cannot exceed 100 characters.";
    }
    if (strlen($short_name) > 20) {
        $errors[] = "Short name cannot exceed 20 characters.";
    }
    if (empty($college_id)) {
        $errors[] = "Please select a college.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("INSERT INTO departments (deptid, deptfullname, deptshortname, deptcollid) 
                                   VALUES (:dept_id, :full_name, :short_name, :college_id)");
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':short_name', $short_name);
            $stmt->bindParam(':college_id', $college_id);

            $stmt->execute();
            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Error: Department ID already exists. Please use a different ID.";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }

    exit();
}

// Endpoint to fetch colleges for the dropdown
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
    $colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($colleges);
    exit();
}
?>

<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $program_id = $_SESSION['edit_id'] ?? null;
    $program_name = trim($input['program_name'] ?? '');
    $program_shortname = trim($input['program_shortname'] ?? '');
    $college_id = trim($input['college_id'] ?? '');
    $department_id = trim($input['department_id'] ?? '');
    $errors = [];

    if (!$program_id) {
        echo json_encode(["success" => false, "errors" => ["Invalid program ID."]]);
        exit();
    }

    if (strlen($program_name) > 100) {
        $errors[] = "Program name cannot exceed 100 characters.";
    }
    if (strlen($program_shortname) > 20) {
        $errors[] = "Program short name cannot exceed 20 characters.";
    }
    if (empty($college_id)) {
        $errors[] = "College ID is required.";
    }
    if (empty($department_id)) {
        $errors[] = "Department ID is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("UPDATE programs 
                                   SET progfullname = :program_name, 
                                       progshortname = :program_shortname, 
                                       progcollid = :college_id, 
                                       progcolldeptid = :department_id 
                                   WHERE progid = :program_id");
            $stmt->bindParam(':program_id', $program_id);
            $stmt->bindParam(':program_name', $program_name);
            $stmt->bindParam(':program_shortname', $program_shortname);
            $stmt->bindParam(':college_id', $college_id);
            $stmt->bindParam(':department_id', $department_id);
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            $errors[] = "Error updating program: " . $e->getMessage();
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }
}

// Endpoint to fetch program details
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['program_id'])) {
    $program_id = $_GET['program_id'];

    $query = $dbh->prepare("SELECT * FROM programs WHERE progid = :program_id");
    $query->bindParam(':program_id', $program_id);
    $query->execute();
    $program = $query->fetch(PDO::FETCH_ASSOC);

    if ($program) {
        echo json_encode($program);
    } else {
        echo json_encode(["success" => false, "errors" => ["Program not found."]]);
    }
}
?>

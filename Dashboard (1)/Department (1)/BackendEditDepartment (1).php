<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $dept_id = $_SESSION['edit_id'] ?? null;
    $full_name = trim($input['full_name'] ?? '');
    $short_name = trim($input['short_name'] ?? '');
    $college_id = trim($input['college_id'] ?? '');
    $errors = [];

    if (!$dept_id) {
        echo json_encode(["success" => false, "errors" => ["Invalid department ID."]]);
        exit();
    }

    if (strlen($full_name) > 100) {
        $errors[] = "Full name cannot exceed 100 characters.";
    }
    if (strlen($short_name) > 20) {
        $errors[] = "Short name cannot exceed 20 characters.";
    }
    if (empty($college_id)) {
        $errors[] = "College ID is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("UPDATE departments 
                                   SET deptfullname = :full_name, 
                                       deptshortname = :short_name, 
                                       deptcollid = :college_id 
                                   WHERE deptid = :dept_id");
            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':short_name', $short_name);
            $stmt->bindParam(':college_id', $college_id);
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            $errors[] = "Error updating department: " . $e->getMessage();
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }
}

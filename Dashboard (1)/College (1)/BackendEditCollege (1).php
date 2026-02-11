<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $college_id = $_SESSION['edit_id'] ?? null;
    $full_name = trim($input['full_name'] ?? '');
    $short_name = trim($input['short_name'] ?? '');
    $errors = [];

    if (!$college_id) {
        echo json_encode(["success" => false, "errors" => ["Invalid college ID."]]);
        exit();
    }

    if (strlen($full_name) > 100) {
        $errors[] = "Full name cannot exceed 100 characters.";
    }
    if (strlen($short_name) > 20) {
        $errors[] = "Short name cannot exceed 20 characters.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("UPDATE colleges 
                                   SET collfullname = :full_name, 
                                       collshortname = :short_name 
                                   WHERE collid = :college_id");
            $stmt->bindParam(':college_id', $college_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':short_name', $short_name);
            $stmt->execute();

            echo json_encode(["success" => true]);
        } catch (PDOException $e) {
            $errors[] = "Error updating college: " . $e->getMessage();
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }
}

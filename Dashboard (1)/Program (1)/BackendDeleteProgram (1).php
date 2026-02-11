<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $program_id = $input['program_id'] ?? null;

    if (!$program_id) {
        echo json_encode(["success" => false, "errors" => ["Invalid program ID."]]);
        exit();
    }

    try {
        $stmt = $dbh->prepare("DELETE FROM programs WHERE progid = :program_id");
        $stmt->bindParam(':program_id', $program_id);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "errors" => ["Error deleting program: " . $e->getMessage()]]);
    }
}
?>

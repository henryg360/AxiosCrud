<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);

    $student_id = $input['student_id'] ?? null;

    if (!$student_id) {
        echo json_encode(["success" => false, "errors" => ["Invalid student ID."]]);
        exit();
    }

    try {
        $stmt = $dbh->prepare("DELETE FROM students WHERE studid = :student_id");
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "errors" => ["Error deleting student: " . $e->getMessage()]]);
    }
}
?>

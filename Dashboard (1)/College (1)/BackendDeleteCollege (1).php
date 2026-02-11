<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

$data = json_decode(file_get_contents("php://input"), true);
$college_id = $data['college_id'] ?? null;

if (!$college_id) {
    echo json_encode(["status" => "error", "message" => "Missing college ID."]);
    exit();
}

$delete_query = $dbh->prepare("DELETE FROM colleges WHERE collid = :collid");
$delete_query->bindParam(':collid', $college_id);

if ($delete_query->execute()) {
    echo json_encode(["status" => "success", "message" => "College deleted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting college."]);
}

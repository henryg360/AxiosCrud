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
$dept_id = $data['dept_id'] ?? null;

if (!$dept_id) {
    echo json_encode(["status" => "error", "message" => "Missing department ID."]);
    exit();
}

$delete_query = $dbh->prepare("DELETE FROM departments WHERE deptid = :deptid");
$delete_query->bindParam(':deptid', $dept_id);

if ($delete_query->execute()) {
    echo json_encode(["status" => "success", "message" => "Department deleted successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting department."]);
}
?>

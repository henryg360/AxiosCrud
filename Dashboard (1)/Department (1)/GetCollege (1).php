<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

// Fetch colleges from the database
$query = "SELECT collid, collfullname FROM colleges";
$stmt = $dbh->prepare($query);
$stmt->execute();
$colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the colleges as JSON
echo json_encode($colleges);
?>

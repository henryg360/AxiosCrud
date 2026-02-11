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
    // Decode JSON input from Axios
    $input = file_get_contents('php://input'); // Read raw POST data
    $data = json_decode($input, true); // Decode JSON to associative array

    // Validate the data
    $college_id = trim($data['college_id'] ?? '');
    $full_name = trim($data['full_name'] ?? '');
    $short_name = trim($data['short_name'] ?? '');

    if (empty($college_id)) {
        $errors[] = "College ID is required.";
    } elseif (!ctype_digit($college_id) || strlen($college_id) > 11) {
        $errors[] = "College ID must be numeric and not exceed more than 11 digits.";
    }
    if (strlen($full_name) > 50) {
        $errors[] = "Full name cannot exceed 50 characters.";
    }
    if (strlen($short_name) > 20) {
        $errors[] = "Short name cannot exceed 20 characters.";
    }

    // If no errors, insert into database
    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("INSERT INTO colleges (collid, collfullname, collshortname) 
                                   VALUES (:college_id, :full_name, :short_name)");
            $stmt->bindParam(':college_id', $college_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':short_name', $short_name);

            $stmt->execute();
            echo json_encode(["success" => true]); // Send success response
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $errors[] = "Error: College ID already exists. Please use a different ID.";
            } else {
                $errors[] = "Database error: " . $e->getMessage(); // Log detailed error
            }
            echo json_encode(["success" => false, "errors" => $errors]);
        }
    } else {
        echo json_encode(["success" => false, "errors" => $errors]);
    }

    file_put_contents('debug.log', print_r($data, true), FILE_APPEND); // Log decoded input for debugging
    exit();
}

// Handle Cancel button
if (isset($_POST["Cancel"])) {
    header("Location: CollegeListings.php");
    exit();
}
?>

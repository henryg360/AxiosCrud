<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

if (isset($_POST["Cancel"])) {
    header("Location: CollegeListings.php");
    exit();
}

if (isset($_POST['edit_id'])) {
    $_SESSION['edit_id'] = $_POST['edit_id'];
}

if (isset($_SESSION['edit_id'])) {
    $college_id = $_SESSION['edit_id'];
} else {
    header("Location: CollegeListings.php");
    exit();
}

// Fetch college details
$query = $dbh->prepare("SELECT * FROM colleges WHERE collid = :collid");
$query->bindParam(':collid', $college_id, PDO::PARAM_STR);
$query->execute();
$college = $query->fetch(PDO::FETCH_ASSOC);

// If no college is found, handle it gracefully
if (!$college) {
    die("Error: No college found with the specified ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit College</title>
    <link rel="stylesheet" href="../../Assets/EditCollege.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="EditCollege.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form id="editCollegeForm">
            <table>
                <tr>
                    <th colspan="2">Edit College Data</th>
                </tr>
                <tr>
                    <td>College ID</td>
                    <td><input type="text" name="college_id" id="collegeId" value="<?= htmlspecialchars($college['collid']) ?>" readonly></td>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td><input type="text" name="full_name" id="fullName" value="<?= htmlspecialchars($college['collfullname']) ?>"></td>
                </tr>
                <tr>
                    <td>Short Name</td>
                    <td><input type="text" name="short_name" id="shortName" value="<?= htmlspecialchars($college['collshortname']) ?>"></td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <button type="submit" id="saveBtn">Save</button>
                        <button type="button" id="cancelBtn" onclick="window.location.href='CollegeListings.php'">Cancel</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <ul id="modalErrorList" style="list-style: none; padding: 0;"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p id="successMessage">College updated successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


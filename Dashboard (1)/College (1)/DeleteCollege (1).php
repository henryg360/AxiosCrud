<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

$college_id = $_POST['delete_id'] ?? $_GET['delete_id'] ?? null;
if (!$college_id) {
    $_SESSION['message'] = "Invalid request.";
    header("Location: CollegeListings.php");
    exit();
}

$query = $dbh->prepare("SELECT collid, collfullname, collshortname FROM colleges WHERE collid = :collid");
$query->bindParam(':collid', $college_id);
$query->execute();
$college = $query->fetch(PDO::FETCH_ASSOC);

if (!$college) {
    $_SESSION['message'] = "College not found.";
    header("Location: CollegeListings.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete College</title>
    <link rel="stylesheet" href="../../Assets/DeleteCollege.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="DeleteCollege.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <table>
            <tr>
                <th colspan="2">Confirm Delete College</th>
            </tr>
            <tr>
                <td>College ID</td>
                <td><input type="text" id="college_id" value="<?= $college['collid'] ?>" readonly></td>
            </tr>
            <tr>
                <td>Full Name</td>
                <td><input type="text" id="full_name" value="<?= $college['collfullname'] ?>" readonly></td>
            </tr>
            <tr>
                <td>Short Name</td>
                <td><input type="text" id="short_name" value="<?= $college['collshortname'] ?>" readonly></td>
            </tr>
            <tr>
                <td colspan="2" class="button-row">
                    <button id="deletebtn">Delete</button>
                    <button id="cancelbtn">Cancel</button>
                </td>
            </tr>
        </table>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete this college? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
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
                    <p id="successMessage">College deleted successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

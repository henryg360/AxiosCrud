<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

$dept_id = $_POST['delete_id'] ?? $_GET['delete_id'] ?? null;
if (!$dept_id) {
    $_SESSION['message'] = "Invalid request.";
    header("Location: DepartmentListing.php");
    exit();
}

$query = $dbh->prepare("SELECT deptid, deptfullname, deptshortname, deptcollid FROM departments WHERE deptid = :deptid");
$query->bindParam(':deptid', $dept_id);
$query->execute();
$department = $query->fetch(PDO::FETCH_ASSOC);

if (!$department) {
    $_SESSION['message'] = "Department not found.";
    header("Location: DepartmentListing.php");
    exit();
}

// Retrieve college name
$collegeQuery = $dbh->prepare("SELECT collfullname FROM colleges WHERE collid = :collid");
$collegeQuery->bindParam(':collid', $department['deptcollid']);
$collegeQuery->execute();
$college = $collegeQuery->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="../../Assets/DeleteDepartment.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="DeleteDepartment.js" defer></script>
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <table class="table table-bordered">
                <tr>
                    <th colspan="2" class="text-center">Confirm Delete Department</th>
                </tr>
                <tr>
                    <td>Department ID</td>
                    <td><input type="text" id="deptId" value="<?= $department['deptid'] ?>" readonly class="form-control"></td>
                </tr>
                <tr>
                    <td>Full Name</td>
                    <td><input type="text" id="fullName" value="<?= $department['deptfullname'] ?>" readonly class="form-control"></td>
                </tr>
                <tr>
                    <td>Short Name</td>
                    <td><input type="text" id="shortName" value="<?= $department['deptshortname'] ?>" readonly class="form-control"></td>
                </tr>
                <tr>
                    <td>College</td>
                    <td><input type="text" id="collegeName" value="<?= $college['collfullname'] ?>" readonly class="form-control"></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">
                        <button id="deletebtn" class="btn btn-danger">Delete</button>
                        <button id="cancelbtn" class="btn btn-secondary">Cancel</button>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p>Are you sure you want to delete this department? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                </div>
                <div class="modal-body">
                    <p id="successMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="successOkBtn">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

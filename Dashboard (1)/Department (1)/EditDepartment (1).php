<?php
session_start();

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

if (isset($_POST["Cancel"])) {
    header("Location: DepartmentListing.php");
    exit();
}

if (isset($_POST['edit_id'])) {
    $_SESSION['edit_id'] = $_POST['edit_id'];
}

$department = null;
if (isset($_SESSION['edit_id'])) {
    $dept_id = $_SESSION['edit_id'];

    $query = $dbh->prepare("SELECT * FROM departments WHERE deptid = :deptid");
    $query->bindParam(':deptid', $dept_id);
    $query->execute();
    $department = $query->fetch(PDO::FETCH_ASSOC);
}

if ($department === false || $department === null) {
    $_SESSION['message'] = "Department not found.";
    header("Location: DepartmentListing.php");
    exit();
}

// Retrieve colleges for the dropdown
$collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dept_id'])) {
    $full_name = trim($_POST['full_name']);
    $short_name = trim($_POST['short_name']);
    $college_id = $_POST['college_id'];

    if (strlen($full_name) > 100) {
        $errors[] = "Full name cannot exceed 100 characters.";
    }
    if (!empty($short_name) && strlen($short_name) > 20) {
        $errors[] = "Short name cannot exceed 20 characters.";
    }
    if (empty($college_id)) {
        $errors[] = "Please select a college.";
    }

    if (empty($errors)) {
        try {
            $stmt = $dbh->prepare("UPDATE departments 
                                   SET deptfullname = :full_name, 
                                       deptshortname = :short_name, 
                                       deptcollid = :college_id 
                                   WHERE deptid = :dept_id");

            $stmt->bindParam(':dept_id', $dept_id);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':short_name', $short_name);
            $stmt->bindParam(':college_id', $college_id);

            $stmt->execute();
            unset($_SESSION['edit_id']);
            header("Location: DepartmentListing.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation (duplicate entry)
                $errors[] = "Error: Duplicate key detected.";
            } else {
                $errors[] = "Error: Could not update department.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
    <link rel="stylesheet" href="../../Assets/EditDepartment.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="EditDepartment.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form id="editDepartmentForm" method="post">
            <table>
                <tr>
                    <th colspan="2">Edit Department Information</th>
                </tr>
                <tr>
                    <td>Department ID</td>
                    <td><input type="text" name="dept_id" id="deptId" value="<?= htmlspecialchars($department['deptid'] ?? '') ?>" readonly></td>
                </tr>
                <tr>
                    <td>Department Name</td>
                    <td><input type="text" name="full_name" id="fullName" value="<?= htmlspecialchars($department['deptfullname'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td>Short Name (optional)</td>
                    <td><input type="text" name="short_name" id="shortName" value="<?= htmlspecialchars($department['deptshortname'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td>College</td>
                    <td>
                        <select name="college_id" id="collegeId">
                            <option value="">Select College</option>
                            <?php foreach ($colleges as $college): ?>
                                <option value="<?= $college['collid'] ?>" <?= $college['collid'] == ($department['deptcollid'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($college['collfullname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <input type="submit" value="Save" id="savebtn">
                        <button type="button" id="cancelBtn" onclick="window.location.href='DepartmentListing.php'">Cancel</button>
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
                    <p id="successMessage">Department updated successfully!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="successOkBtn">OK</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

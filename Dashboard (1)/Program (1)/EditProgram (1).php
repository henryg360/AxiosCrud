<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../usjrLogin.php");
    exit();
}

if (isset($_POST['edit_id'])) {
    $_SESSION['edit_id'] = $_POST['edit_id'];
}

$program = null;
if (isset($_SESSION['edit_id'])) {
    $program_id = $_SESSION['edit_id'];

    $dbh = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $dbh->prepare("SELECT * FROM programs WHERE progid = :program_id");
    $query->bindParam(':program_id', $program_id);
    $query->execute();
    $program = $query->fetch(PDO::FETCH_ASSOC);
}

if ($program === false || $program === null) {
    $_SESSION['message'] = "Program not found.";
    header("Location: ProgramListing.php");
    exit();
}

// Retrieve colleges for the dropdown
$collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve departments for the current college
$departments = [];
if ($program) {
    $departmentsQuery = $dbh->prepare("SELECT deptid, deptfullname FROM departments WHERE deptcollid = :college_id");
    $departmentsQuery->bindParam(':college_id', $program['progcollid']);
    $departmentsQuery->execute();
    $departments = $departmentsQuery->fetchAll(PDO::FETCH_ASSOC);
}

$errors = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Program</title>
    <link rel="stylesheet" href="../../Assets/EditProgram.css">
    <script src="../../axios/axios.min.js"></script>
    <script src="EditProgram.js" defer></script>
    <!-- Add Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="form-container">
        <form id="editProgramForm">
            <input type="hidden" id="programId" value="<?= htmlspecialchars($_SESSION['edit_id'] ?? '') ?>">
            <table>
                <tr>
                    <th colspan="2">Edit Program Information</th>
                </tr>

                <tr>
                    <td colspan="2">
                        <ul class="error-list" style="color: red; display: none;"></ul>
                    </td>
                </tr>

                <tr>
                    <td>Program ID</td>
                    <td><input type="text" name="program_id" id="programIdInput" value="<?= htmlspecialchars($program['progid'] ?? '') ?>" readonly></td>
                </tr>
                <tr>
                    <td>Program Name</td>
                    <td><input type="text" name="program_name" id="programName" value="<?= htmlspecialchars($program['progfullname'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td>Program Short Name</td>
                    <td><input type="text" name="program_shortname" id="programShortName" value="<?= htmlspecialchars($program['progshortname'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td>College</td>
                    <td>
                        <select name="college_id" id="collegeDropdown">
                            <option value="">Select College</option>
                            <?php foreach ($colleges as $college): ?>
                                <option value="<?= $college['collid'] ?>" <?= $college['collid'] == ($program['progcollid'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($college['collfullname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Department</td>
                    <td>
                        <select name="department_id" id="departmentDropdown">
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department['deptid'] ?>" <?= $department['deptid'] == ($program['progcolldeptid'] ?? '') ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($department['deptfullname']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="button-row">
                        <button type="submit" id="savebtn">Save</button>
                        <button type="button" id="cancelbtn" onclick="window.location.href='ProgramListing.php'">Cancel</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                </div>
                <div class="modal-body">
                    <ul id="errorMessage"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
                    <p id="successMessage">Program successfully updated!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="successOkBtn">Close</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

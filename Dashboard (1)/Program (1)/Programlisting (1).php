<?php
session_start(); 
if (!isset($_SESSION['username'])) { 
    header("Location: ../usjrLogin.php");
    exit(); 
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

// Retrieve programs
$programsQuery = $dbh->query("SELECT programs.progid, programs.progfullname, programs.progshortname, departments.deptid, departments.deptfullname AS department, colleges.collid, colleges.collfullname AS college 
                              FROM programs
                              JOIN departments ON programs.progcolldeptid = departments.deptid
                              JOIN colleges ON departments.deptcollid = colleges.collid");
$programs = $programsQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve colleges for the dropdown filter
$collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve departments for the dropdown filter
$departmentsQuery = $dbh->query("SELECT deptid, deptfullname, deptcollid FROM departments");
$departments = $departmentsQuery->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["addprogram"])) {
    header("Location: AddProgram.php");
    exit();
}

if (isset($_POST["goback"])) {
    header("Location: ../../AdminHomepage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program List</title>
    <link rel="stylesheet" href="../../Assets/ListingProgram.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <header class="header-container">
        <form action="" method="post" id="headerForm">
            <div class="header-content">
                <div class="left-section addnewprogram">
                    <input type="submit" name="addprogram" id="addbtn" value="Add New Program">
                </div>
                <div class="right-section logout-header">
                    <span>You are logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <input type="submit" name="goback" value="Go Back" id="gobackbtn">
                </div>
            </div>
        </form>
    </header>

    <div class="filter-container">
        <label for="collegeFilter">Filter by College:</label>
        <select id="collegeFilter">
            <option value="">All Colleges</option>
            <?php foreach ($colleges as $college): ?>
                <option value="<?= $college['collid'] ?>"><?= $college['collfullname'] ?></option>
            <?php endforeach; ?>
        </select>
        <label for="departmentFilter">Filter by Department:</label>
        <select id="departmentFilter">
            <option value="">All Departments</option>
            <?php foreach ($departments as $department): ?>
                <option value="<?= $department['deptid'] ?>" data-college="<?= $department['deptcollid'] ?>"><?= $department['deptfullname'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <table>
        <tr class="title-header">
            <th>ID</th>
            <th>Program Full Name</th>
            <th>Program Short Name</th>
            <th>Department</th>
            <th>College</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($programs as $program): ?>
        <tr data-college="<?= $program['collid'] ?>" data-department="<?= $program['deptid'] ?>">
            <td><?= $program['progid'] ?></td>
            <td><?= $program['progfullname'] ?></td>
            <td><?= $program['progshortname'] ?></td>
            <td><?= $program['department'] ?></td>
            <td><?= $program['college'] ?></td>
            <td> 
                <!-- Edit Button -->
                <form action="EditProgram.php" method="post" class="icon-button-form">
                    <input type="hidden" name="edit_id" value="<?= $program['progid'] ?>">
                    <button type="submit" name="edit" class="icon-button">
                        <i class="bi bi-pencil-square" style="font-size: 2rem; color: green"></i>
                    </button>
                </form>
                <!-- Delete Button -->
                <form action="DeleteProgram.php" method="post" class="icon-button-form"> 
                    <input type="hidden" name="delete_id" value="<?= $program['progid'] ?>"> 
                    <button type="submit" name="delete" class="icon-button">
                        <i class="bi bi-trash3-fill" style="font-size: 2rem; color: red"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Include External JS -->
    <script src="scripts/logout.js"></script>
</body>
</html>

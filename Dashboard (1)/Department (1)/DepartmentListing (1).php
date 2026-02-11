<?php
session_start(); 
if (!isset($_SESSION['username'])) { 
    header("Location: ../../usjrLogin.php");
    exit(); 
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

$departmentsQuery = $dbh->query("SELECT departments.deptid, departments.deptfullname, departments.deptshortname, colleges.collid, colleges.collfullname AS college 
                                 FROM departments
                                 JOIN colleges ON departments.deptcollid = colleges.collid");
$departments = $departmentsQuery->fetchAll(PDO::FETCH_ASSOC);

$collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["adddepartment"])) {
    header("Location: AddDepartment.php");
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
    <title>Department List</title>
    <link rel="stylesheet" href="../../Assets/ListingDepartment.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="header-container">
        <form action="" method="post">
            <div class="header-content">
                <div class="left-section addnewdepartment">
                    <input type="submit" name="adddepartment" id="addbtn" value="Add New Department">
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
    </div>

    <table>
        <tr class="title-header">
            <th>ID</th>
            <th>Department Name</th>
            <th>Short Name</th>
            <th>College</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($departments as $department): ?>
        <tr data-college="<?= $department['collid'] ?>">
            <td><?= $department['deptid'] ?></td>
            <td><?= $department['deptfullname'] ?></td>
            <td><?= $department['deptshortname'] ?></td>
            <td><?= $department['college'] ?></td>
            <td> 
                <!-- Edit Button -->
                <form action="EditDepartment.php" method="post" class="icon-button-form">
                    <input type="hidden" name="edit_id" value="<?= $department['deptid'] ?>">
                    <button type="submit" name="edit" class="icon-button">
                        <i class="bi bi-pencil-square" style="font-size: 2rem; color: green"></i>
                    </button>
                </form>
                <!-- Delete Button -->
                <form action="DeleteDepartment.php" method="post" class="icon-button-form"> 
                    <input type="hidden" name="delete_id" value="<?= $department['deptid'] ?>"> 
                    <button type="submit" name="delete" class="icon-button">
                        <i class="bi bi-trash3-fill" style="font-size: 2rem; color: red"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <script>
        document.getElementById('collegeFilter').addEventListener('change', function() {
            var selectedCollege = this.value;
            var rows = document.querySelectorAll('table tr[data-college]');
            rows.forEach(row => {
                if (selectedCollege === "" || row.getAttribute('data-college') === selectedCollege) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>

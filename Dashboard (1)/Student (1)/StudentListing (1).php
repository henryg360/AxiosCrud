<?php
session_start(); 
if (!isset($_SESSION['username'])) { 
    header("Location: ../usjrLogin.php");
    exit(); 
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

// Retrieve students
$studentsQuery = $dbh->query("SELECT students.studid, students.studlastname, students.studfirstname, students.studmidname, colleges.collid, colleges.collfullname AS college, programs.progfullname AS program, students.studyear 
                              FROM students
                              JOIN programs ON students.studprogid = programs.progid
                              JOIN colleges ON students.studcollid = colleges.collid");
$students = $studentsQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve colleges for the dropdown filter
$collegesQuery = $dbh->query("SELECT collid, collfullname FROM colleges");
$colleges = $collegesQuery->fetchAll(PDO::FETCH_ASSOC);

// Retrieve programs for the dropdown filter
$programsQuery = $dbh->query("SELECT programs.progid, programs.progfullname, programs.progcollid 
                              FROM programs
                              JOIN departments ON programs.progcolldeptid = departments.deptid
                              JOIN colleges ON programs.progcollid = colleges.collid");
$programs = $programsQuery->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["addstudent"])) {
    header("Location: AddStudent.php");
    exit();
}

if (isset($_POST["goback"])) {
    header("Location: ../../AdminHomepage.php");  // Redirect to AdminHomepage.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="../../Assets/ListingStudent.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="header-container">
        <form action="" method="post">
            <div class="header-content">
                <div class="left-section addnewstudent">
                    <input type="submit" name="addstudent" id="addbtn" value="Add New Student">
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
        <label for="programFilter">Filter by Program:</label>
        <select id="programFilter">
            <option value="">All Programs</option>
            <?php foreach ($programs as $program): ?>
                <option value="<?= $program['progid'] ?>" data-college="<?= $program['progcollid'] ?>"><?= $program['progfullname'] ?></option>
            <?php endforeach; ?>
        </select>
        <label for="searchFilter">Search by:</label>
        <select id="searchType">
            <option value="studid">Student Number</option>
            <option value="studlastname">Last Name</option>
            <option value="studfirstname">First Name</option>
        </select>
        <input type="text" id="searchInput" placeholder="Search...">
    </div>
    <table>
        <tr class="title-header">
            <th>ID</th>
            <th>Last Name</th>
            <th>First Name</th>
            <th>Middle Initial</th>
            <th>College</th>
            <th>Program Enrolled</th>
            <th>Year</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($students as $student): ?>
        <tr data-college="<?= $student['collid'] ?>" data-studid="<?= $student['studid'] ?>" data-studlastname="<?= $student['studlastname'] ?>" data-studfirstname="<?= $student['studfirstname'] ?>" data-program="<?= $student['program'] ?>">
            <td><?= $student['studid'] ?></td>
            <td><?= $student['studlastname'] ?></td>
            <td><?= $student['studfirstname'] ?></td>
            <td><?= $student['studmidname'] ?></td>
            <td><?= $student['college'] ?></td>
            <td><?= $student['program'] ?></td>
            <td><?= $student['studyear'] ?></td>
            <td> 
                <!-- Edit Button -->
                <form action="EditStudent.php" method="post" class="icon-button-form">
                    <input type="hidden" name="edit_id" value="<?= $student['studid'] ?>">
                    <button type="submit" name="edit" class="icon-button">
                        <i class="bi bi-pencil-square" style="font-size: 2rem; color: green"></i>
                    </button>
                </form>
                <!-- Delete Button -->
                <form action="DeleteStudents.php" method="post" class="icon-button-form"> 
                    <input type="hidden" name="delete_id" value="<?= $student['studid'] ?>"> 
                    <button type="submit" name="delete" class="icon-button">
                        <i class="bi bi-trash3-fill" style="font-size: 2rem; color: red"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <script>
        const programs = <?php echo json_encode($programs); ?>;

        document.getElementById('collegeFilter').addEventListener('change', function() {
            const selectedCollege = this.value;
            const programFilter = document.getElementById('programFilter');
            
            // Filter program options based on selected college
            programFilter.innerHTML = '<option value="">All Programs</option>';
            programs.forEach(program => {
                if (selectedCollege === "" || program.progcollid == selectedCollege) {
                    const option = document.createElement('option');
                    option.value = program.progid;
                    option.textContent = program.progfullname;
                    programFilter.appendChild(option);
                }
            });

            filterStudents();
        });

        document.getElementById('programFilter').addEventListener('change', function() {
            filterStudents();
        });

        document.getElementById('searchType').addEventListener('change', function() {
            filterStudents();
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            filterStudents();
        });

        function filterStudents() {
            var selectedCollege = document.getElementById('collegeFilter').value;
            var selectedProgram = document.getElementById('programFilter').value;
            var searchType = document.getElementById('searchType').value;
            var searchInput = document.getElementById('searchInput').value.toLowerCase();
            var rows = document.querySelectorAll('table tr[data-college]');
            rows.forEach(row => {
                var collegeMatch = selectedCollege === "" || row.getAttribute('data-college') === selectedCollege;
                var programMatch = selectedProgram === "" || row.getAttribute('data-program') === selectedProgram;
                var searchMatch = searchInput === "" || row.getAttribute('data-' + searchType).toLowerCase().includes(searchInput);
                if (collegeMatch && programMatch && searchMatch) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        document.getElementById('collegeFilter').dispatchEvent(new Event('change'));
    </script>
</body>
</html>


<?php
session_start(); 
if (!isset($_SESSION['username'])) { 
    header("Location: usjrLogin.php");
    exit(); 
}

if (isset($_POST['students'])) {
    header("Location: Dashboard/Student/StudentListing.php");
    exit();
}
if (isset($_POST['colleges'])) {
    header("Location: Dashboard/College/CollegeListings.php");
    exit();
}
if (isset($_POST['departments'])) {
    header("Location: Dashboard/Department/DepartmentListing.php");
    exit();
}
if (isset($_POST['programs'])) {
    header("Location: Dashboard/Program/Programlisting.php");
    exit();
}
if (isset($_POST['logout_confirm'])) {
    session_destroy();
    header("Location: usjrLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="Assets/AdminHomepage.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="header">
        <img src="Assets/LOGO.png" alt="University Logo" class="logo">
        <h1>UNIVERSITY OF SAN JOSE-RECOLETOS</h1>
        <div class="user-info">
            <span>Your Logged In as: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <button class="logout-btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</button>
        </div>
    </div>
    <div class="content">
        <form action="" method="post" class="menu-container">
            <button type="submit" name="colleges" class="menu-item">
                <i class="bi bi-building"></i> Colleges
            </button>
            <button type="submit" name="departments" class="menu-item">
                <i class="bi bi-briefcase"></i> Departments
            </button>
            <button type="submit" name="programs" class="menu-item">
                <i class="bi bi-mortarboard"></i> Programs
            </button>
            <button type="submit" name="students" class="menu-item">
                <i class="bi bi-person"></i> Students
            </button>
        </form>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                </div>
                <div class="modal-body">
                    Do you wish to logout?
                </div>
                <div class="modal-footer">
                    <form action="" method="post">
                        <button type="submit" name="logout_confirm" id="confirmLogout" class="btn btn-danger">Yes</button>
                    </form>
                    <button type="button" id="cancelLogout" class="btn btn-primary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

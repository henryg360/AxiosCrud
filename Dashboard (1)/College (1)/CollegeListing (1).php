<?php
session_start(); 
if (!isset($_SESSION['username'])) { 
    header("Location: ../../usjrLogin.php");
    exit(); 
}

$dbs = "mysql:host=localhost;dbname=usjr";
$dbpn = "root";
$dbh = new PDO($dbs, $dbpn, $dbpn);

$query = $dbh->query("SELECT collid, collfullname, collshortname FROM colleges");
$colleges = $query->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: usjrLogin.php");
    exit();
}

if (isset($_POST["addcollege"])) {
    header("Location: AddCollege.html");
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
    <title>College List</title>
    <link rel="stylesheet" href="../../Assets/ListingCollege.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header class="header-container">
        <form action="" method="post">
            <div class="header-content">
                <div class="left-section addnewcollege">
                    <input type="submit" name="addcollege" id="addbtn" value="Add New College">
                </div>
                <div class="right-section logout-header">
                    <span>You are logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <input type="submit" name="goback" value="Go Back" id="gobackbtn">
                    <input type="submit" name="logout" value="Logout" id="logoutbtn">
                </div>
            </div>
        </form>
    </header>
    <table>
        <tr class="title-header">
            <th>College ID</th>
            <th class="full-name">College Full Name</th>
            <th class="short-name">College Short Name</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($colleges as $college): ?>
        <tr>
            <td><?= $college['collid'] ?></td>
            <td class="full-name"><?= $college['collfullname'] ?></td>
            <td class="short-name"><?= $college['collshortname'] ?></td>
            <td class="icon-buttons">

                <form action="EditCollege.html" method="post" class="icon-button-form">
                    <input type="hidden" name="edit_id" value="<?= $college['collid'] ?>">
                    <button type="submit" name="edit" class="icon-button">
                        <i class="bi bi-pencil-square" style="font-size: 2rem; color: green"></i>
                    </button>
                </form>

                <form action="DeleteCollege.html" method="post" class="icon-button-form">
                    <input type="hidden" name="delete_id" value="<?= $college['collid'] ?>"> 
                    <button type="submit" name="delete" class="icon-button">
                        <i class="bi bi-trash3-fill" style="font-size: 2rem; color: red"></i>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

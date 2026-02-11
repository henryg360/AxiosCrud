<?php
session_start();
$dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($user) || empty($password)) {
        $error_message = "Input username and password.";
    } else {
        $sql = "SELECT * FROM appusers WHERE name = ?";
        $statement = $dbconnect->prepare($sql);
        $statement->bindParam(1, $user, PDO::PARAM_STR);
        $statement->execute();

        $user_data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            if (password_verify($password, $user_data['password'])) {
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['username'] = $user_data['name'];
                header('Location: AdminHomepage.php');
                exit();
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "Username not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="Assets/usjrlogin.css">
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="header-container">
        <h1>User Login</h1>
    </div>

    <div class="container">
        <form id="loginForm" action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user ?? ''); ?>">
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <br>

            <div class="button-container">
                <button type="submit">Login</button>
                <button type="reset">Clear</button>
            </div>
        </form>
        
        <p class="login-link">
            Don't have an account? <a href="usjrRegistration.php">Register here</a>
        </p>
    </div>

    <!-- Modal HTML -->
    <div class="modal fade" id="errorModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Login Error</h5>
                </div>
                <div class="modal-body" id="modalMessage">
                    <!-- Error message dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add your custom JS -->
    <script src="Assets/Modals/usjrlogin.js"></script>

    <!-- Pass error message to JS -->
    <script>
        const serverErrorMessage = "<?php echo htmlspecialchars($error_message); ?>";
    </script>

</body>
</html>

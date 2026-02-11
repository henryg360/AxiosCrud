<?php
// Start session and enable output buffering to avoid header issues
ob_start();
session_start();

try {
    // Database connection
    $dbconnect = new PDO("mysql:host=localhost;dbname=usjr", "root", "root");
    $dbconnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if (empty($user) || empty($password) || empty($confirm_password)) {
        $error_message = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error_message = "Password must be at least 8 characters long and include at least one letter and one number.";
    } else {
        // Check if username already exists
        $sql = "SELECT * FROM appusers WHERE name = ?";
        $statement = $dbconnect->prepare($sql);
        $statement->bindParam(1, $user, PDO::PARAM_STR);
        $statement->execute();

        $user_data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $error_message = "Username already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $sql = "INSERT INTO appusers (name, password) VALUES (?, ?)";
            $statement = $dbconnect->prepare($sql);
            $statement->bindParam(1, $user, PDO::PARAM_STR);
            $statement->bindParam(2, $hashed_password, PDO::PARAM_STR);

            if ($statement->execute()) {
                $success_message = "User created.";
            } else {
                $error_message = "Registration failed. Please try again.";
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
    <title>User Registration</title>
    <link rel="stylesheet" href="Assets/usjrregistration.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header-container">
            <h1>User Registration</h1>
        </div>

        <form id="registrationForm" action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required>
            <br>

            <div class="button-container">
                <button type="submit">Register</button>
                <button type="reset">Clear</button>
            </div>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="usjrLogin.php">Login here</a></p>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Message</h5>
                </div>
                <div class="modal-body" id="modalMessage"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="modalOkButton">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Assets/Modals/usjrregistration.js"></script>
    <script>
        const errorMessage = "<?php echo htmlspecialchars($error_message); ?>";
        const successMessage = "<?php echo htmlspecialchars($success_message); ?>";
    </script>
</body>
</html>

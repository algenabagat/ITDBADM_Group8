<?php 
require_once 'config.php';
$conn = getDBConnection($host, $user, $password, $database, $port);

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];

$checkEmail = "SELECT * FROM users WHERE email = '$email'";
$isRegistered = $conn->query($checkEmail);

if ($isRegistered->num_rows > 0) {
    $errM = "Email is already registered";
    header("Location: signup.php?error=$errM");
    exit();
}
else {
    if ($password !== $confirmPassword) {
        $errM = "Passwords do not match";
        header("Location: signup.php?error=$errM");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $insertUser = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role_id) VALUES (?, ?, ?, ?, 3)");
    $insertUser->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);
    $insertUser->execute();

    header("Location: login.php?success=Account created successfully. Please log in.");
    exit();
}
?>

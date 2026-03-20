<?php
// register.php - Stores a hashed password to the database
// Student: Mahlatse Mphelo | Module: WEDE6021

session_start();
require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = ['type' => 'error', 'text' => 'Please fill in all fields.'];
    } else {
        // Check if username already exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param('s', $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = ['type' => 'error', 'text' => 'Username already taken.'];
        } else {
            // Hash the password using bcrypt before storing
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param('ss', $username, $hashed);

            if ($stmt->execute()) {
                $message = ['type' => 'success', 'text' => 'Account created! Password was hashed and stored. <a href="login.php">Login here</a>.'];
            } else {
                $message = ['type' => 'error', 'text' => 'Something went wrong. Try again.'];
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Password Hashing</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; margin: 0; padding: 30px; }
        .container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c7be5; }
        label { display: block; margin-top: 14px; margin-bottom: 4px; font-weight: bold; font-size: 14px; }
        input { width: 100%; padding: 9px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px; }
        input:focus { border-color: #2c7be5; outline: none; }
        button { width: 100%; padding: 10px; margin-top: 18px; background: #2c7be5; color: white; border: none; border-radius: 4px; font-size: 15px; cursor: pointer; }
        button:hover { background: #1a5dc8; }
        .error   { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 10px; font-size: 14px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 10px; font-size: 14px; }
        .link { text-align: center; margin-top: 14px; font-size: 13px; }
        .link a { color: #2c7be5; }
        .hint { font-size: 11px; color: #888; margin-bottom: 2px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <p style="text-align:center; color:#666; font-size:13px;">Password Hashing Demo</p>

    <?php if ($message): ?>
        <div class="<?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="e.g. mahlatse"
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"/>

        <label for="password">Password</label>
        <div class="hint">This will be hashed with bcrypt before being saved</div>
        <input type="password" id="password" name="password" placeholder="Enter a password"/>

        <button type="submit">Register</button>
    </form>

    <div class="link">Already have an account? <a href="login.php">Login here</a></div>
</div>
</body>
</html>

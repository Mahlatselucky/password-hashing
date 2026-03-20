<?php
// login.php - Retrieves hash from database and verifies the entered password
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
        // Retrieve the stored hash for this username
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hash);
        $stmt->fetch();

        if ($stmt->num_rows > 0 && password_verify($password, $hash)) {
            // password_verify() compares the entered password against the stored hash
            $message = ['type' => 'success', 'text' => 'Login successful! Password matched the stored hash.'];
        } else {
            $message = ['type' => 'error', 'text' => 'Incorrect username or password.'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Password Hashing</title>
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
    <h2>Login</h2>
    <p style="text-align:center; color:#666; font-size:13px;">Password Hashing Demo</p>

    <?php if ($message): ?>
        <div class="<?php echo $message['type']; ?>"><?php echo $message['text']; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username"
               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"/>

        <label for="password">Password</label>
        <div class="hint">password_verify() will compare this against the stored hash</div>
        <input type="password" id="password" name="password" placeholder="Enter your password"/>

        <button type="submit">Login</button>
    </form>

    <div class="link">No account yet? <a href="register.php">Register here</a></div>
</div>
</body>
</html>

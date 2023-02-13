<!DOCTYPE html>
<html>
<head>
  <title>Login Form</title>
</head>
<body style="background:pink">
  <h2>Login Form</h2>
  <form action="login.php" method="post">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username">
    <br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <br><br>
    <input type="submit" value="Submit">
  </form>
</body>
</html>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  
  // Verify the username and password against a database or some other method
  if ($username === 'admin' && $password === 'secret') {
    // Login successful
    // Store the username in the session
    $_SESSION['username'] = $username;
    // Redirect to a protected page or show a success message
    header('Location: index.php');
    exit;
  } else {
    // Login failed
    // Show an error message or redirect back to the login form
    echo 'Incorrect username or password';
  }
}
?>



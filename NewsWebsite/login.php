<?php 
    session_start();
    include ('database.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Log in</title>

    <div class = "nav-log">
        <ul>
            <li><a href="news.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </div>

</head>
<body>
<h1>Login now!</h1>
<form method = "post" action = "login.php">
<!--whole php block script copied from https://www.youtube.com/watch?v=ShbHwaiyOps&feature=youtu.be&ab_channel=AwaMelvine-->
<?php if (count($errors) > 0): ?>
   <div class = "error">
    <?php foreach ($errors as $error): ?>
        <p><?php echo $error; ?></p>
    <?php endforeach ?>
    </div>
<?php endif ?>
<br>
<input type = "text" name = "username" id = "username" required>
<input type = "password" name = "password" id = "password" required>
<input type = "submit" name = "loginUser" id = "loginUser" value = "Login">
<p>
    Don't have an account? <a href="register.php">Register now!</a>
</p>
</form>

</body>
</html>
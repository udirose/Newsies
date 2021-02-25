<?php
session_start();
$errors = array();
$mysqli = new mysqli('localhost', 'news', 'newsies2', 'news');

        if ($mysqli->connect_errno) {
            printf("Connection Failed: %s\n", $mysqli->connect_error);
            exit;
        }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Newsies</title>

    <div class="nav">
        <ul>
            <li><a href="news.php">Home</a></li>
            <?php if ($_SESSION['username'] == null): ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif ?>
            <?php if ($_SESSION['username'] != null): ?>
                <li><a href="post.php">Post</a></li>
                <li><a href="profile.php">Profile</a></li>
            <?php endif ?>
        </ul>
    </div>
</head>


<body>
    <h1>Newsies</h1>
    <form method="post" action="news.php">
        
        <?php
        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            echo "<h3>Welcome $username</h3>";
        } else {
            echo "<h2>You are not logged in. <a href = 'login.php'>Click here to login</a></h2>";
        }
        ?>

        <?php
        
             $stmt2 = $mysqli->prepare("select id from users where username= ?");
             
             if(!$stmt2){
               printf("Query Prep Failed: %s\n", $mysqli->error);
               exit;
             }
             
             $stmt2->bind_param('s', $username);
             
             $stmt2->execute();
             
             $stmt2->bind_result($id);
             
            
             while($stmt2->fetch()){
                $_SESSION['userid'] = $id;
             }
            
   
             
             $stmt2->close();
        ?>

        <?php if ($_SESSION['username'] != null): ?>
        <input type="submit" name="logout" id="logout" value="Logout">
        <?php endif ?>
    </form>

    <?php

    if (isset($_POST['logout'])) {
        unset($_SESSION['username']);
        session_destroy();
        header('location: news.php');
    }

    ?>

    <!--whole php block script copied from https://www.youtube.com/watch?v=ShbHwaiyOps&feature=youtu.be&ab_channel=AwaMelvine-->
<?php if (count($errors) > 0): ?>
   <div class = "error">
    <?php foreach ($errors as $error): ?>
        <p><?php echo $error; ?></p>
    <?php endforeach ?>
    </div>
<?php endif ?>



    <form action="news.php" method="POST">
        <?php
        
        

        $stmt = $mysqli->prepare("select user, title, time, id from stories");
        if (!$stmt) {
            printf("Query Prep Failed: %s\n", $mysqli->error);
            exit;
        }

        $stmt->execute();

        $stmt->bind_result($user, $title, $time, $id);
        echo "<br>";
        //echo "<ul>\n";
        while ($stmt->fetch()) {
            $timestamp = strtotime($time);
            // echo ", ";
            // echo date('Y-m-d H:i:s', $timestamp);

            //echo "<label for ='$id' style = 'text-align:center;'><span><b>" . $title . " by: " . $user . ", " . date('Y-m-d H:i:s', $timestamp) . "</b></span></label><br>";
            $date = date('Y-m-d H:i:s', $timestamp);
            //echo "<input type = 'submit' style = 'text-align:center;' name = 'storybutton' id = '$id' value = '$id'/>";
            echo "<button type = 'submit' id = '$id' name = 'storybutton' value = '$id'>" . htmlentities($title) . " by: " . htmlentities($user) . ", " . date('Y-m-d H:i:s', htmlentities($timestamp)) . "</button>";
            echo "<br>";
            echo "<br>";
        }
        //echo "</ul>\n";

        $stmt->close();
           
            

        ?>
        
    </form>
    <?php
            
    if(isset($_POST['storybutton'])){
        $_SESSION['storyID'] = $_POST['storybutton'];
        echo $_POST['storybutton'];
        echo "hello";
        header('location:view.php');
    }
 ?>



    
</body>

</html>
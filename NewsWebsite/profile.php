<?php
session_start();
if ($_SESSION['username'] == null){
    array_push($errors, "You are not logged in. Please login to view your profile.");
    header('location: news.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Newsies</title>
    <div class="nav-prof">
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
<h2>Update Profile</h2>
    <?php
        $username = $_SESSION['username'];
        echo "hello ". htmlentities($username);
        $mysqli = new mysqli('localhost', 'news', 'newsies2', 'news');

        if($mysqli->connect_errno) {
            printf("Connection Failed: %s\n", $mysqli->connect_error);
            exit;
        }
    ?>


<form action="profile.php" method="POST">
    <strong>If you would like to edit or delete one of your stories, select it below.</strong>
    <?php
    $user = $_SESSION['username'];
    $stmt = $mysqli->prepare("select title, time, id from stories where user= ?");
    $stmt->bind_param('s', $user);
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }
    
    $stmt->execute();
    
    $stmt->bind_result($title, $time, $id);
    $title = htmlentities($title);
    echo "<ul>\n";
    while($stmt->fetch()){
      $timestamp = strtotime($time);
      $timestamp = htmlentities($timestamp);
        echo "<input type = 'radio' name = 'storybutton' id = '$id' required value = '$id';/>". $title .", ". date('Y-m-d H:i:s', $timestamp) ."<br>";
    }
    echo "</ul>\n";
    
    $stmt->close();
    ?>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    <input type="submit" id = "delete" name = "delete" value="DELETE">
    <input type="submit" id = "edit" name = "edit" value = "EDIT">
    <br>
    <br>
    
</form>

<?php
    if(isset($_POST['delete'])){
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
        $id = $mysqli->real_escape_string($_POST['storybutton']);
        $mysqli->query("DELETE FROM stories WHERE id=$id"); 
        header('location:profile.php');
    }

    if(isset($_POST['edit'])){
        $id = $mysqli->real_escape_string($_POST['storybutton']);       
        $currentStory = "SELECT id,story,title,link FROM stories WHERE id = '$id'";
        $resultcheck = mysqli_query($mysqli, $currentStory);
        $resultValue = mysqli_fetch_row($resultcheck);
        $_SESSION['editStory'] = $resultValue[1];
        $_SESSION['editTitle'] = $resultValue[2];
        $_SESSION['editLink'] = $resultValue[3];
        $_SESSION['editID'] = $resultValue[0];
        header('location: edit.php');
    }
?>

<form action='profile.php' method='POST'>
    <strong>If you would like to edit or delete one of your comments, select it below.</strong>
    <?php
    $user = $_SESSION['username'];
    $stmt = $mysqli->prepare("select user, time, comment, commentid from comments where user= ?");
    $stmt->bind_param('s', $user);
    if(!$stmt){
      printf("Query Prep Failed: %s\n", $mysqli->error);
      exit;
    }
    
    $stmt->execute();
    
    $stmt->bind_result($user, $time, $comment, $commentid);
    $comment = htmlentities($comment);
    echo "<ul>\n";
    while($stmt->fetch()){
      $timestamp = strtotime($time);
      $timestamp = htmlentities($timestamp);
        echo "<input type = 'radio' name = 'storybutton' id = '$commentid' value = '$commentid' style = 'text-align:center; font-family: avenir; user-select: all;'/>". $comment .", ". date('Y-m-d H:i:s', $timestamp) ."<br>";
    }
    echo "</ul>\n";
    
    $stmt->close();
    ?>
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    <input type="submit" id = "commentDelete" name = "commentDelete" value="DELETE">
    <input type="submit" id = "commentEdit" name = "commentEdit" value="EDIT">
   
    <?php
    if(isset($_POST['commentDelete'])){
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
      $commentid = $mysqli->real_escape_string($_POST['storybutton']);
      $mysqli->query("DELETE FROM comments WHERE commentid=$commentid"); 
      header('location: profile.php');
    }
    if(isset($_POST['commentEdit'])){
        //echo "<input type = 'text' name = 'newComment' id = 'newComment' value = '$editComment'";
       $_SESSION['commentid'] = $mysqli->real_escape_string($_POST['storybutton']);
       header('location:edit2.php');
}
       
    ?>
</form>




    <form action="profile.php" method="post">
    <h2>Update Information:</h2>
        <label for="newUsername">Enter new username here:</label>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type="text" name="newUsername" id="newUsername"/>
        <input type="submit" name="updateUser" id="updateUser" value="Set new username"/>
    </form>
    <?php
        if(isset($_POST['updateUser'])){
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
            $username = $mysqli->real_escape_string($_POST['newUsername']);
            $check = "SELECT * FROM users WHERE username = '$username'";
            $resultcheck = mysqli_query($mysqli, $check);
            $ifexists = mysqli_num_rows($resultcheck);
            if($ifexists <= 0){
                $id = $_SESSION['userid'];
              
                $query = "UPDATE users SET username = '$username' WHERE id = $id";
                $result = mysqli_query($mysqli, $query);
    
                if($result){
                    echo "Update succesful";
                    $_SESSION['username'] = $username;
                } else{
                    echo "Update failed";
                }
                header('location: post.php');

            }
            else{
                array_push($errors, "This username already exists. Please log in instead.");
            }
        }
    ?>




<form action="profile.php" method="post">
        <br>
        <label for="newPass">Enter new password here:</label>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type="password" name="newPass" id="newPass"/>
        <input type="submit" name="updatePass" id="updatePass" value="Set new password"/>
    </form>
    <?php
        if(isset($_POST['updatePass'])){
            if(!hash_equals($_SESSION['token'], $_POST['token'])){
                die("Request forgery detected");
            }
            $password = $mysqli->real_escape_string($_POST['newPass']);
                $id = $mysqli->real_escape_string($_SESSION['userid']);
                $encryptedPass = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET password = '$encryptedPass' WHERE id = $id";
                $result = mysqli_query($mysqli, $query);
    
                if($result){
                    echo "Update succesful";
                } else{
                    echo "Update failed";
                }
                header('location: post.php');
        }
    ?>




</body>
</html>
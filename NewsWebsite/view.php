<?php
    session_start();
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
    <div class="nav-v">
        <ul>
            <li><a href="news.php">Home</a></li>
            <?php if ($_SESSION['username'] == null): ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif ?>
            <?php if ($_SESSION['username'] != null): ?>
                <li><a href="post.php">Post</a></li>
                <li><a href = "profile.php">Profile</a></li>
            <?php endif ?>
        </ul>
    </div>
</head>
<body>
    
<h2>Newsies</h2>
<br><br>

<?php
$id = (int)$_SESSION['storyID'];
$story = $mysqli->prepare("select story from stories where id = $id");
$link = $mysqli->prepare("select link from stories where id = $id");
$storyTitle = $mysqli->prepare("select title from stories where id = $id");
$author = $mysqli->prepare("select user from stories where id = $id");

//display story title
if($storyTitle){
    $storyTitle->execute();
    $storyTitle->bind_result($title);
    $title = htmlentities($title);
    while($storyTitle->fetch()){
        echo "<h2 style = 'text-align:center;'>$title</h2>";
    }

}
$storyTitle->close();

//display author
if($author){
    $author->execute();
    $author->bind_result($name);
    $name = htmlentities($name);
    while($author->fetch()){
        echo "<h3 style = 'text-align:center;'>by $name</h3>";
    }
}
$author->close();

//display text
if ($story) {
    $story->execute();
    $story->bind_result($text);
    $text = htmlentities($text);
    while ($story->fetch()) {
        echo "<p style = 'text-align:center;'>$text</p>";
        
    }
}
$story->close();

//display link
if($link){
    $link->execute();
    $link->bind_result($storylink);
    $storylink = htmlentities($storylink);
    while ($link->fetch()){
        echo " ";
        echo "<a href = '$storylink' style = 'text-align:center' target='_blank' rel='noreferrer noopener'>$storylink</a>";
    }
}
$link->close();
?>

<h3>Comments:</h3>

<?php
$id = (int)$_SESSION['storyID'];
$user = $_SESSION['username'];
$stmt = $mysqli->prepare("select user, time, comment, commentid from comments where id= ?");
$stmt->bind_param('s', $id);
if(!$stmt){
  printf("Query Prep Failed: %s\n", $mysqli->error);
  exit;
}

$stmt->execute();

$stmt->bind_result($user, $time, $comment, $commentid);
echo "<br>";
$user = htmlentities($user);
$comment = htmlentities($comment);
while($stmt->fetch()){
  $timestamp = strtotime($time);
  $timestamp = htmlentities($timestamp);
    echo "<br>".$user." said: ". $comment. " on: ". date('Y-m-d', $timestamp)." at ".date('H:i:s', $timestamp)."</br>";
}

$stmt->close();


?>

<?php if ($_SESSION['username'] != null): ?>
<form action="view.php" method="POST">
    <label for="comment">Enter comment here:</label>
    <input type="text" name="comment" id="comment" />
    <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    <input type="submit" id = "submitComment" name = "submitComment" value="Post your comment"/>
</form>
<?php endif ?>
<?php
    if(isset($_POST['submitComment'])){
        if(!hash_equals($_SESSION['token'], $_POST['token'])){
            die("Request forgery detected");
        }
    $id = (int)$_SESSION['storyID'];
    $user = $_SESSION['username'];
    $comment = $_POST['comment'];
    $insert = $mysqli->prepare("insert into comments (id, user, comment) values (?, ?, ?)");
    if(!$insert){
	      printf("Query Prep Failed: %s\n", $mysqli->error);
	  exit;
    }

    $insert->bind_param('sss', $id, $user, $comment);

    $insert->execute();

    $insert->close();
    header('location: view.php');
}
?>

<form method = "get" action = "https://www.google.com/search">
<br>
<button type = 'submit' id = '$id' name = 'q' value = '<?php echo $text?>'>More Stories Like this</button>
</form>



</body>
</html>
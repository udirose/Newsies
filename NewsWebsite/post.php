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

  <div class = "nav-p">
        <ul>
            <li><a href="news.php">Home</a></li>
            <?php if ($_SESSION['username'] == null): ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif ?>
            <li><a href = "post.php">Post</a></li>
            <li><a href = "profile.php">Profile</a></li>
        </ul>
    </div>
</head>
<body>


<form action="post.php" method="POST">
    <h2>Create a Post</h2>
    <br>
<p>
        <label for="title">Write your title here:</label>
        <input type="text" name="title" id="title" value = "<?php echo $editTitle;?>"/>
    </p>
    <p>
        <label for="story">Write your story here:</label>
        <input type="text" name="story" id="story" style = "height: 100px; margin: auto; position: relative;" value = "<?php echo $editStory;?>"/>
    </p>
    <p>
        <label for="link">Enter link here if applicable:</label>
        <input type="url" name="link" id="link" value = "<?php echo $editLink;?>"/>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
    </p>
    <p>
		<input type="submit" name = "submitStory" id = "submitStory" value="Submit" />
	</p>
</form>


<?php
    

    $mysqli = new mysqli('localhost', 'news', 'newsies2', 'news');
    if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
    }
    if(isset($_POST['submitStory'])){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
}
    $user = $_SESSION['username'];
    $title = $_POST['title'];
    $story = $_POST['story'];
    $link = $_POST['link'];
   
    
        $stmt = $mysqli->prepare("insert into stories (user, title, story, link) values (?, ?, ?, ?)");
        if(!$stmt){
            printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
        }
        $stmt->bind_param('ssss', $user, $title, $story, $link);
        $stmt->execute();

        $stmt->close();

?>






</body>
</html>

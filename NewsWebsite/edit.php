<?php
session_start();
$mysqli = new mysqli('localhost', 'news', 'newsies2', 'news');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
if ($_SESSION['username'] == null){
    array_push($errors, "You are not logged in. Please login to view your profile.");
    header('location: news.php');
}

  $editStory = $_SESSION['editStory'];
  $editStory = htmlentities($editStory);
  $editTitle = $_SESSION['editTitle'];
  $editTitle = htmlentities($editTitle);
  $editLink = $_SESSION['editLink'];
  $editLink = htmlentities($editLink);
  $id = (int)$_SESSION['editID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Newsies</title>
    <div class = "nav-edit">
        <ul>
            <li><a href="news.php">Home</a></li>
            <li><a href = "post.php">Post</a></li>
            <li><a href = "profile.php">Profile</a></li>
        </ul>
    </div>
</head>
<body>

<form action="edit.php" method="POST">
<p>
        <label for="title">Write your title here:</label>
        <input type="text" name="title" id="title" value = "<?php echo $editTitle?>"/>
    </p>
    <p>
        <label for="story">Write your story here:</label>
        <input type="text" name="story" id="story" style = "height: 100px; margin: auto; position: relative"; value = "<?php echo $editStory?>"/>
    </p>
    <p>
        <label for="link">Enter link here if applicable:</label>
        <input type="url" name="link" id="link" value = "<?php echo $editLink?>"/>
    </p>
    <p>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type = "submit" value = "Cancel" name = "cancel" id = "cancel"/>
		<input type="submit" value="Update" name = "update" id = "update"/> 
    
	  </p>
</form>

<?php
if(isset($_POST['update'])){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
    $user = $_SESSION['username'];
    $title = $_POST['title'];
    $story = $_POST['story'];
    $link = $_POST['link'];
    
    $user = $mysqli->real_escape_string($user);
    $story = $mysqli->real_escape_string($story);
    $title = $mysqli->real_escape_string($title);
    $link = $mysqli->real_escape_string($link);
   
    $query = "UPDATE stories SET title = '$title', story = '$story', link = '$link' WHERE id = $id";
    $result = mysqli_query($mysqli, $query);
    
    if($result){
        echo "Update succesful";
    } else{
        echo "Update failed";
    }
    header('location: profile.php');

    // $sql = "UPDATE stories SET title = ?, story = ?, link = ? WHERE id = ? LIMIT 1";
    // $result = $mysqli->prepare($sql);
    // $result->bind_param($title,$story,$link,$_SESSION['editID']);
    // $result->execute();
    // if($result){
        
    //     echo $result->affected_rows . " Row Updated <br>";
    // }
    
    // $result->close();
}
if(isset($_POST['cancel'])){
    header('location: profile.php');
}
?>
    
</body>
</html>
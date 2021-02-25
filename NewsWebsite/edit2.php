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
        $commentid = $_SESSION['commentid'];   
        $currentComment = "SELECT comment,id FROM comments WHERE commentid = $commentid";
        $resultcheck = mysqli_query($mysqli, $currentComment);
        $resultValue = mysqli_fetch_row($resultcheck);
        $editComment = $resultValue[0];
        $_SESSION['editCommentStory'] = $resultValue[1];
        $editComment = htmlentities($editComment);
        $user = $_SESSION['username'];
        $user = $mysqli->real_escape_string($user);
        $id = $mysqli->real_escape_string($_SESSION['editCommentStory']);
?>       

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Newsies</title>
</head>
<body>
    <form method = "post" action = "edit2.php">
        <h2>Edit your comment:</h2>
        <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>" />
        <input type = "text" name = "newComment" id = "newComment" value = '<?php echo $editComment ?>'>
        <input type = "submit" name = "submitNewComment" id = "submitNewComment"/>
    </form>
    <form method = "post" action = "profile.php">
        <input type = "submit" name = "cancel" id = "cancel" value = "Cancel"/>
    </form>
</body>
</html>

<?php
   if(isset($_POST['submitNewComment'])){
    if(!hash_equals($_SESSION['token'], $_POST['token'])){
        die("Request forgery detected");
    }
    $newComment = (string)$_POST['newComment'];
    $query = "UPDATE comments SET comment = '$newComment' WHERE commentid = $commentid";
    $result = mysqli_query($mysqli, $query);
    
    if($result){
        echo "Update succesful";
    } else{
        echo "Update failed";
    }
    header('location: profile.php');
    }

    ?>
<?php
session_start();
// a lot from https://www.youtube.com/watch?v=ShbHwaiyOps&feature=youtu.be&ab_channel=AwaMelvine
$username = "";
$password = "";
$errors = array();
//Copied from the 330 wiki
// Content of database.php
$mysqli = new mysqli('localhost', 'news', 'newsies2', 'news');

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}

if(isset($_POST['submitNewUser'])){

    $username = $mysqli->real_escape_string($_POST['newUser']);
    $check = "SELECT * FROM users WHERE username = '$username'";
    $resultcheck = mysqli_query($mysqli, $check);
    $ifexists = mysqli_num_rows($resultcheck);

    if($ifexists <=0){
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
        $password = $mysqli->real_escape_string($_POST['newPass']);
        $encryptedPass = password_hash($password, PASSWORD_DEFAULT);
        //$sql = "INSERT INTO users (username,password) VALUES ('$username','$$encryptedPass')";

        $stmt = $mysqli->prepare("INSERT INTO users (id,username,password) VALUES (null,?,?)");
        $stmt->bind_param("ss",$username,$encryptedPass);
        $result = $stmt->execute();
        if($result){
            $_SESSION['username'] = $username;
            header('location: news.php');
        }else{
            array_push($errors, "There was an error.");
        }
    } else {
        array_push($errors, "This username already exists. Please log in instead.");
    }
}



if(isset($_POST['loginUser'])){

    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $mysqli->real_escape_string($_POST['password']);

    //a lot from https://www.youtube.com/watch?v=Q-fBhFTe2H8&ab_channel=shadsluiter
    $stmt = $mysqli->prepare("SELECT id, username, password FROM users where username = ?");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($userid, $uname, $pw);

    if($stmt->num_rows==1){
        $stmt->fetch();
        if(password_verify($password, $pw)){
            $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
            $_SESSION['username'] = $uname;
            $_SESSION['userid'] = $userid;
            header('location:news.php');
        } else {
            array_push($errors, "Incorrect username or password. Please try again.");
            session_destroy();
        }
    } else {
        array_push($errors, "Incorrect username or password. Please try again.");
        session_destroy();
    }

}


?>
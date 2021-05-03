<?php include '../db/dbconn.php';
session_start();
$mysqli = openConnection();
$username = $_SESSION['username'];
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $result=$mysqli->query("select * from Users where Username='$username'")  or die($mysqli->error);
    $user = $result->fetch_assoc();
    $userid = $user['id'];
    $title = $_POST['title'];
    $content = (isset($_POST['content']))? $_POST['content'] : $_POST['imageLink'];
    $time  = microtime(true);
    $subreddit = $_POST['subreddit'];
    $sql = 'INSERT INTO posts (title, user_id, is_image, body, created_at, updated_at, subreddit_name)' . "VALUES ('$title','$userid', ". (isset($_POST['image'])?"1":"0") .", '$content', '$time', '$time', '$subreddit')"; 
    $mysqli->query($sql) or die($mysqli->error);
    $newpost = $mysqli->insert_id;
    $result->free();
    closeConnection($mysqli);
    header("location: ../post.php?postID=".$newpost);  
}

?>
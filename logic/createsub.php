<!-- CREATE SUBREDDIT -->
<?php include '../db/dbconn.php';
session_start();
$mysqli = openConnection();
$userid = $_SESSION['userid'];
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = "INSERT INTO subreddits (name, creator_id, description, created_at)"."VALUES ('".$mysqli->real_escape_string($_POST['name'])."', ".$userid.", '".$mysqli->real_escape_string($_POST['description'])."',".microtime(true).")";
    $mysqli->query($sql);
    header("location: ../index.php"); 
}
closeConnection($mysqli);

?>
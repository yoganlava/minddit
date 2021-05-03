<?php
include '../db/dbconn.php';
$time  = microtime(true);
if(!isset($_POST['parentID'])){
$sql = "INSERT INTO comments (parent_id, user_id, post_id, body, created_at, updated_at)" .
"VALUES (null, '".$_POST['userid']."','".$_POST['postID']."','".$_POST['body']."','".$time."','".$time."')";
}else{
    $sql = "INSERT INTO comments (parent_id, user_id, post_id, body, created_at, updated_at)" .
"VALUES (".$_POST['parentID'].", '".$_POST['userid']."','".$_POST['postID']."','".$_POST['body']."','".$time."','".$time."')";
}
$mysqli = openConnection();
$mysqli->query($sql) or die($mysqli->error);
closeConnection($mysqli);
?>
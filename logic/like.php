<?php
include '../db/dbconn.php';
session_start();
$mysqli = openConnection();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $sql = "CALL insert_likes(".$_POST['userid'].",'".$_POST['likeType']."', ".$_POST['contentID'].", '".$_POST['contentType']."')";
        $mysqli->query($sql) or die($mysqli->error);
}
?>
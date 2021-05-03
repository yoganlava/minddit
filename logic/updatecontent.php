<?php 
include '../db/dbconn.php';
session_start();
$mysqli = openConnection();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['contentType'] === "comment"){
        $sql = "UPDATE comments set body = '".$_POST['comment']."', updated_at = ".microtime(true)."where id = ".$_POST['id'];
    }else if($_POST['contentType'] === "post"){
        $sql = "UPDATE posts set body = '".$_POST['comment']."', updated_at = ".microtime(true)."where id = ".$_POST['id'];
    }

    $mysqli->query($sql) or die($mysqli->error);
}
?>
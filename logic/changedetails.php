<?php
include '../db/dbconn.php';
session_start();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $mysqli = openConnection();
    $sql = "UPDATE details SET profile_picture = '".$_POST['profile_picture']."' , gender = '".$_POST['gender']."', country = '".$_POST['country']."', age = ".$_POST['age'].", description = '".$_POST['description']."' where user_id = ".$_SESSION['userid'];
    $mysqli->query($sql) or die($mysqli->error);
    $_SESSION['message'] = "Profile successfully updated!";
    header("location: ../details.php");
}
?>
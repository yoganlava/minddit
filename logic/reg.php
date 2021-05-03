<?php
include '../db/dbconn.php';
session_start();
$mysqli = openConnection();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $mysqli->real_escape_string($_POST['username']);  
    $password = $mysqli->real_escape_string($_POST['password']);

    $check=mysqli_query($mysqli,"select * from Users where Username='$username'");  
    $checkrows=mysqli_num_rows($check);  
    
    if($checkrows>0) {  
          $_SESSION['message'] = "Register unsuccessful, please choose a different name!";
          closeConnection($mysqli);
          header("location: ../register.php");
    } else {
        $sql = 'INSERT INTO users (username, password, created_at)' . "VALUES ('$username','$password', ".microtime(true).")";  
        $mysqli->query($sql) or die($mysqli->error);
        $_SESSION['username'] = $username;

        $result = $mysqli->query("select * from Users where Username='$username'");  
        $user =  $result->fetch_assoc();
        $_SESSION['userid'] = $user['id'];
        closeConnection($mysqli);
        header("location: ../index.php");  
    }
        


}
?>
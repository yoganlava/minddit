<?php 
include '../db/dbconn.php';
session_start();
$mysqli = openConnection();

if($_SERVER['REQUEST_METHOD'] == 'POST'){  
    $username = $mysqli->real_escape_string($_POST['username']);  
    $password = $mysqli->real_escape_string($_POST['password']);  
    $result=$mysqli->query("select * from Users where Username='$username'");  
    if($result->num_rows == 0) {  
        $_SESSION['message'] = "No user of that name was found!";  
        CloseConnection($mysqli);
        header("location: ../login.php");
    } else {
        $user = $result->fetch_assoc();
        if($user['password'] != $password){
            $_SESSION['message'] = "Password incorrect!";
            CloseConnection($mysqli);
            header("location: ../login.php");
        }else{
            $_SESSION['username'] = $user['username'];
            $_SESSION['userid'] = $user['id'];
            CloseConnection($mysqli);  
            header("location: ../index.php");  
        }
    }
}
    

?>
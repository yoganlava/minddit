<?php
include '../db/dbconn.php';
session_start();
$mysqli = openConnection();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $sql = "INSERT INTO subreddit_subscriptions (subreddit_name, user_id)"."VALUES ('".$_POST['name']."',".$_POST['userid'].")";
    if($mysqli->query($sql)){
        echo "Subscribed!";
    }else{
        echo "Already subscribed!";
    }
}
closeConnection($mysqli);
?>
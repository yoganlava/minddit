<?php
include '../db/dbconn.php';
$mysqli = openConnection();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $call = "CALL insert_awards (".$_POST['userid'].",'".$_POST['awardType']."', ".$_POST['contentID'].", '".$_POST['contentType']."', @awardStatus);";
        $select = "SELECT @awardStatus as awardStatus;";
        $mysqli->query($call);
        $status = ($mysqli->query($select))->fetch_assoc();
        echo ($status['awardStatus'])? "You have successfully given the award!":"You have already given an award to this ".$_POST['contentType'];
}
?>
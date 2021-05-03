<?php  
function openConnection() {  
    $config = parse_ini_file('sql.cnf');  
    $dbhost = $config['host'];  
    $dbuser = $config['username'];  
    $dbpass = $config['password'];  
    $db = $config['db'];  
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db);  
    return $conn;  
}  
function closeConnection($conn) {  
    $conn -> close();  
}  
?>  

<?php

require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $dataDB);

$DBServer2 = 'swaytwig.com'; // e.g 'localhost' or '192.168.1.100'
$DBUser2   = 'opendb';
$DBPass2   = 'opendb';
$DBName2   = 'opendb';

$conn2 = new mysqli($DBServer2, $DBUser2, $DBPass2, $DBName2);


$sql = "SELECT * FROM db_ship_drop ORDER BY uid DESC LIMIT 1";

$rs =$conn->query($sql);
$row = $rs->fetch_assoc();
//var_dump($row);
$sql = "SELECT * FROM db_ship_drop WHERE regis BETWEEN '" . $row['regis'] . "' AND NOW()";
echo $sql;
$rs = $conn2->query($sql);
/*while($row = $rs->fetch_assoc()){
	var_dump($row);
	echo "<br>";
}*/
echo $rs->num_rows;

?>
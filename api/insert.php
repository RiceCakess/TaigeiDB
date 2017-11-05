<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../util/config.php');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);
/*$sql = "SELECT world,map,node,maprank, count(*) as count FROM db_ship_drop GROUP BY world, map, node, (CASE WHEN `world` > 6 THEN `maprank` END)";
$rs = $conn->query($sql);

while($row = $rs->fetch_assoc()){
	$w = $row['world'];
	$m = $row['map'];
	$n = $row['node'];
	$mr = $row['maprank'];
	$count = $row['count'];
	$checkInsert = 'SELECT * FROM db_node_counts WHERE world=$w AND map=$m AND node=$n AND maprank=$mr';
	$checkRS = $conn->query($checkInsert);
	$insert = "INSERT INTO `db_node_counts`(`world`, `map`, `node`, `maprank`, `count`) VALUES ($w,$m,$n,$mr,$count)";
	if($checkRS && $checkRS->num_rows > 0){
		$insert = 'UPDATE `db_node_counts` SET count=$count WHERE world=$w AND map=$m AND node=$n AND maprank=$mr';
	}
	echo $insert;
	$conn->query($insert);
}*/

//$sql = "UPDATE kancolledb.ships SET craftable=1 WHERE id = ANY (SELECT DISTINCT result FROM kancolle.db_ship_dev)"

?>
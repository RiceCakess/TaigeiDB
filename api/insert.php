<?php
require_once('../util/config.php');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);
/*$now = new DateTime('tomorrow');
$today = $now->format('Y-m-d') . " 00:00:00";
$yearAgo = date_sub($now, new DateInterval("P1Y"))->format('Y-m-d') . " 00:00:00";
$sql = "SELECT world,map,node,maprank, count(*) as count FROM db_ship_drop WHERE (regis BETWEEN '$yearAgo' AND '$today') GROUP BY world, map, node, (CASE WHEN `world` > 6 THEN `maprank` END)";
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
	//echo $insert;
	$conn->query($insert);
}

//$sql = "UPDATE kancolledb.ships SET craftable=1 WHERE id = ANY (SELECT DISTINCT result FROM kancolle.db_ship_dev)"
*/
?>
<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../util/data_db.php';
require '../util/functions.php';

if(isset($_SERVER['PATH_INFO'])){
	$request = trim($_SERVER['PATH_INFO'],'/');
	if($request==="flagship")
		require 'equip_dev_flagship.php';
	else
		http_response_code(400);
	
	return;
}

$equipid = 0;
$sort = "most";
$limit = 5;		
$response = array();

if(isset($_GET['equipid']))
	$equipid = $conn->real_escape_string($_GET["equipid"]);
else{
	http_response_code(400);
	return;
}

if(isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] <=10)
	$limit = $conn->real_escape_string($_GET["limit"]);

if(isset($_GET['sort']) && $_GET['sort'] === "best"){
	$sort = $_GET['sort'];
}
$sql = "SELECT *, COUNT(uid) totalCount 
		FROM db_equip_dev 
		WHERE result = $equipid 
		GROUP BY fuel,ammo,steel,bauxite 
		ORDER BY COUNT(*) DESC 
		LIMIT $limit";

$rs = $conn->query($sql);
while($row = $rs->fetch_assoc()){
	$f = $row['fuel'];
	$a = $row['ammo'];
	$s = $row['steel'];
	$b = $row['bauxite'];
	$totalSql = "SELECT Count(uid) attempts FROM db_equip_dev WHERE fuel=$f AND ammo=$a AND steel=$s AND bauxite=$b";
	$totalRs = $conn->query($totalSql);
	$attempts = $totalRs->fetch_assoc()["attempts"];
	$obj = [
		"fuel" => $f,
		"ammo" => $a,
		"steel" => $s,
		"bauxite" => $b,
		"count" => $row['totalCount'],
		"attempts" => $attempts,
		"percent" => $row['totalCount']/$attempts
	];
	$response[] = $obj;
}
echo json_encode($response);
?>
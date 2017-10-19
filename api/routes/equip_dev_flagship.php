<?php 
header('Content-Type: application/json');
require '../util/config.php';
require '../util/functions.php';

$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);

$equipid = 0;
$limit = $conn->real_escape_string(getLimit(5,10));
$result = array();
$lsc = false;
$msc = microtime(true);

if(isset($_GET['id']) && is_numeric($_GET['id']))
	$equipid = $conn->real_escape_string($_GET["id"]);
else
	error(400,"Invalid Ship ID");

$sql = "SELECT flagship, COUNT(uid) totalCount 
		FROM db_equip_dev 
		WHERE result=$equipid 
		GROUP BY flagship
		ORDER BY COUNT(*) DESC 
		LIMIT $limit";

$rs = $conn->query($sql);
if(!$rs) 
	error(500, "Database Error");

while($row = $rs->fetch_assoc()){
	$obj = [
		"flagship" => $row['flagship'],
		"count" => $row['totalCount']
	];
	$result[] = $obj;
}

$msc = microtime(true)-$msc;
$response = array(
				"id"=>$equipid,
				"queryTime"=> round($msc,3),
				"numResults"=>count($result),
				"data"=>$result
			);
echo json_encode($response, JSON_PRETTY_PRINT);
?>
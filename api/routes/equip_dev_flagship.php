<?php 
$equipid = 0;
$limit = 5;		
$response = array();

require_once '../util/data_db.php';
require_once '../util/functions.php';

if(isset($_GET['equipid']))
	$equipid = $conn->real_escape_string($_GET["equipid"]);
else{
	http_response_code(400);
	return;
}

if(isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] <=10)
	$limit = $conn->real_escape_string($_GET["limit"]);

if(isset($_GET['sort']) && $_GET['sort'] === "recent"){
	$sort = $_GET['sort'];
}

$sql = "SELECT flagship, COUNT(uid) totalCount 
		FROM db_equip_dev 
		WHERE result = $equipid
		GROUP BY flagship
		ORDER BY COUNT(*) DESC 
		LIMIT $limit";

$rs = $conn->query($sql);
while($row = $rs->fetch_assoc()){
	$obj = [
		"flagship" => $row['flagship'],
		"count" => $row['totalCount']
	];
	$response[] = $obj;
}

echo json_encode($response);
?>
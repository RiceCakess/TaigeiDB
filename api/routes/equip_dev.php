<?php 
header('Content-Type: application/json');
require '../util/config.php';
require '../util/functions.php';
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);

$equipid = 0;
$limit = $conn->real_escape_string(getLimit(5,20));
$result = array();
$lsc = false;
$msc = microtime(true);

if(isset($_GET['id']) && is_numeric($_GET['id']))
	$equipid = $conn->real_escape_string($_GET["id"]);
else
	error(400,"Invalid Equip ID");

$sql = "SELECT fuel,ammo,steel,bauxite, COUNT(*) AS `totalCount`,
		(SELECT COUNT(*) 
			FROM db_equip_build T2 
			WHERE T2.fuel=T1.fuel 
			AND T2.ammo=T1.ammo 
			AND T2.steel=T1.steel 
			AND T2.bauxite=T1.bauxite) AS attempts 
		FROM db_equip_build T1 
		WHERE result=$equipid 
		GROUP BY fuel,ammo,steel,bauxite
		ORDER BY attempts DESC
		LIMIT $limit";
$rs = $conn->query($sql);
if(!$rs) error(500, "Database Error");
while($row = $rs->fetch_assoc()){
	$f = $row['fuel'];
	$a = $row['ammo'];
	$s = $row['steel'];
	$b = $row['bauxite'];
	$attempts = $row["attempts"];
	$obj = [
		"fuel" => $f,
		"ammo" => $a,
		"steel" => $s,
		"bauxite" => $b,
		"count" => $row['totalCount'],
		"attempts" => $attempts
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
http_response_code(200);
?>
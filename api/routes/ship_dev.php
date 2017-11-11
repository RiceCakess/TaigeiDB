<?php 
header('Content-Type: application/json');
require '../util/config.php';
require '../util/functions.php';
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);

$shipid = 0;
$limit = $conn->real_escape_string(getLimit(5,20));
$result = array();
$lsc = false;

if(isset($_GET['id']) && is_numeric($_GET['id']))
	$shipid = $conn->real_escape_string($_GET["id"]);
else
	error(400,"Invalid Ship ID");

if(isset($_GET['lsc'])){
	switch($_GET['lsc']){
		case "true": 
			$lsc = true; break;
		case "false": 
			$lsc = false; break;
		default:
			error(400,'To see LSC results, set "lsc"=true');
	}
}

$where = "AND T1.fuel < 1000
		AND T1.ammo < 1000
		AND T1.steel < 1000
		AND T1.bauxite < 1000";
if($lsc){
	$where = "AND T1.fuel > 1000
		AND T1.ammo > 1000
		AND T1.steel > 1000
		AND T1.bauxite > 1000";
}
$sql = "SELECT fuel,ammo,steel,bauxite,material,COUNT(*) AS `totalCount`,
		(SELECT COUNT(*) 
			FROM db_ship_build T2 
			WHERE T2.fuel=T1.fuel 
			AND T2.ammo=T1.ammo 
			AND T2.steel=T1.steel 
			AND T2.bauxite=T1.bauxite 
			AND T2.material=T1.material) AS attempts 
		FROM db_ship_build T1 
		WHERE result=$shipid "
		. $where .
		" GROUP BY fuel,ammo,steel,bauxite,material
		ORDER BY attempts DESC
		LIMIT $limit";
$msc = microtime(true);
$rs = $conn->query($sql);
if(!$rs) error(500, "Database Error");
while($row = $rs->fetch_assoc()){
	$f = $row['fuel'];
	$a = $row['ammo'];
	$s = $row['steel'];
	$b = $row['bauxite'];
	$m = $row['material'];
	$attempts = $row["attempts"];
	$obj = [
		"fuel" => $f,
		"ammo" => $a,
		"steel" => $s,
		"bauxite" => $b,
		"material" => $m,
		"count" => $row['totalCount'],
		"attempts" => $attempts
	];
	$result[] = $obj;
}
$msc = microtime(true)-$msc;
$response = array(
				"id"=>$shipid,
				"queryTime"=> round($msc,3),
				"numResults"=>count($result),
				"data"=>$result
			);
echo json_encode($response, JSON_PRETTY_PRINT);
http_response_code(200);
?>
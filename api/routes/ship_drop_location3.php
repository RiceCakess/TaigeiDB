<?php 
require_once '../util/config.php';
require_once '../util/functions.php';
header('Content-Type: application/json');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);

$world = 0;
$map = 0;
$node = 0;
$maprank = 0;
$result = array();
$now = new DateTime('tomorrow');
$today = $now->format('Y-m-d') . " 00:00:00";
$yearAgo = date_sub($now, new DateInterval("P1Y"))->format('Y-m-d') . " 00:00:00";

if(isset($_GET['world']) && isset($_GET['map']) && is_numeric($_GET['world']) && is_numeric($_GET['map'])){
	$world = $conn->real_escape_string($_GET["world"]);
	$map = $conn->real_escape_string($_GET["map"]);
}
else
	error(400,"Invalid world or map");

if(isset($_GET['maprank']) && $world > 6){
	if(!is_numeric($_GET['maprank']) || $_GET['maprank'] > 3) error(400,"Invalid maprank!");
	$maprank = $conn->real_escape_string($_GET["maprank"]);
}
$sql = "SELECT shp.*,main.world, main.map, main.node, info.letter, main.maprank, main.result, Scount, Acount, Bcount, success 
FROM (SELECT world,map,node,maprank,result,COUNT(*) AS success, 
COUNT(CASE WHEN rank = 'S' THEN 1 END) AS Scount, 
COUNT(CASE WHEN rank = 'A' THEN 1 END) AS Acount, 
COUNT(CASE WHEN rank = 'B' THEN 1 END) AS Bcount  
FROM opendb.db_ship_drop 
WHERE (regis BETWEEN '$yearAgo' AND '$today') 
AND world=$world
AND map=$map
AND (CASE WHEN  world > 6 THEN maprank=$maprank ELSE 1 END) 
GROUP BY world, map, node, (CASE WHEN world > 6 THEN maprank END), result) main 
LEFT JOIN kancolle.nodes info
ON main.world=info.world 
AND main.map=info.map 
AND main.node=info.id
LEFT JOIN kancolle.ships shp
ON main.result=shp.id 
GROUP BY world, map, (CASE WHEN letter is NOT NULL THEN letter ELSE node END), main.result
ORDER BY info.letter ASC, shp.exclusive DESC, success DESC";
//echo $sql;
$msc = microtime(true);
$nodes = [];
$rs = $conn->query($sql);
if(!$rs) error(500,"Database Error");

while($row = $rs->fetch_assoc()){
	$letter = $row['letter'];
	if(!$letter)
		$letter = "Node_" . $row['node'];
	
	//$count = $nodes[$row['world'] . "-" . $row['map'] . "-" . $letter];
	if(!array_key_exists($letter,$result)){
		$result[$letter]["attempts"] = $row["success"];
	}
	else
		$result[$letter]["attempts"] += $row["success"];
	//if(!$row['id'])
		//echo $row['id'] . " " . $row["success"] . " , ";

		$obj = [
			//"result"=>$row["result"],
			"ship"=>["id"=>$row['id'], "en_us"=>$row["en_us"], "asset"=>$row["asset"], "exclusive"=>$row["exclusive"]],
			"Srank" => $row['Scount'],
			"Arank" => $row['Acount'],
			"Brank" => $row['Bcount'],
			"success" => $row['success']
		];
	$result[$letter]["drops"][] = $obj;
}
$msc = microtime(true)-$msc;
$response = array(
				"world"=>$world,
				"map"=>$map,
				"maprank" => $maprank,
				"queryTime"=> round($msc,3),
				"numResults"=>count($result),
				"data"=>$result
			);
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
?>
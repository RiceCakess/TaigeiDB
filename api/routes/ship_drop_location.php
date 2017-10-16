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

if(isset($_GET['world']) && isset($_GET['map']) && is_numeric($_GET['world']) && is_numeric($_GET['map'])){
	$world = $conn->real_escape_string($_GET["world"]);
	$map = $conn->real_escape_string($_GET["map"]);
}
else
	errorCode(400);

if(isset($_GET['node'])){
	if(!is_numeric($_GET['node'])) errorCode(400);
	$node = $conn->real_escape_string($_GET["node"]);
}
if(isset($_GET['maprank']) && $world > 6){
	if(!is_numeric($_GET['maprank'])) errorCode(400);
	$maprank = $conn->real_escape_string($_GET["maprank"]);
}
$sql = "SELECT  main.world, main.map, main.node, main.maprank, main.result,
		COUNT(*) AS totalCount , 
		COUNT(CASE WHEN rank = 'S' THEN 1 END) AS Scount, 
		COUNT(CASE WHEN rank = 'A' THEN 1 END) AS Acount, 
		COUNT(CASE WHEN rank = 'B' THEN 1 END) AS Bcount 
		FROM (SELECT * FROM kancolle.db_ship_drop ";

$nodesql = "SELECT * FROM kancolle.db_node_counts main 
			INNER JOIN kancolledb.nodes sub ON main.world=sub.world AND main.map=sub.map AND main.node=sub.id
			WHERE main.world=$world AND main.map=$map AND (CASE WHEN  main.world > 6 THEN main.maprank=$maprank ELSE 1 END)";
if($node){
	$sql .= "WHERE world=$world AND map=$map AND node=$node AND (CASE WHEN  world > 6 THEN maprank=$maprank ELSE 1 END)) main
			GROUP BY result, (CASE WHEN main.world > 6 THEN main.maprank END)
			ORDER BY result ASC, node ASC";
	$nodesql .= " AND main.node=$node";
}
else{
	$sql .="WHERE world=$world AND map=$map AND (CASE WHEN  world > 6 THEN maprank=$maprank ELSE 1 END)) main
			GROUP BY node, result, (CASE WHEN main.world > 6 THEN main.maprank END)
			ORDER BY node ASC, result ASC";
}

$msc = microtime(true);
$nodes = [];

$rs = $conn->query($nodesql);
if(!$rs) errorCode(500);
while($row = $rs->fetch_assoc()){
	$data = array("count"=>$row['count'], "letter"=>$row['letter']);
	$nodes[$row['world'] . "-" . $row['map'] . "-" . $row['id']] = $data;
}
$rs = $conn->query($sql);
if(!$rs) errorCode(500);

while($row = $rs->fetch_assoc()){
	$nodedata = $nodes[$row['world'] . "-" . $row['map'] . "-" . $row['node']];
	$letter = $nodedata["letter"];
	$id = $row['node'];
	if(!array_key_exists($letter,$result)){
		$result[$letter] = array(
			"id" => $id,
			"attempts" => $nodedata['count'],
			"drops" => array()
		);
	}
	$dupe = false;
	$arr = &$result[$letter]["drops"];
	//check if same node, but different routing and combine results
	for($i =0; $i< count($arr); $i++){
		$drop_obj = &$arr[$i];
		if($drop_obj["result"] == $row['result']){
			$drop_obj['Srank'] += $row['Scount'];
			$drop_obj["Arank"] += $row['Acount'];
			$drop_obj["Brank"] += $row['Bcount'];
			$drop_obj["totalCount"] += $row['totalCount'];
			$result[$letter]["attempts"] += $nodedata['count'];
			$dupe = true;
		}
	}
	if(!$dupe){
		$obj = [
			"result"=>$row["result"],
			"Srank" => $row['Scount'],
			"Arank" => $row['Acount'],
			"Brank" => $row['Bcount'],
			"totalCount" => $row['totalCount']
		];
		$result[$letter]["drops"][] = $obj;
	}
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
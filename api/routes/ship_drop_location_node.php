<?php 
require_once '../util/config.php';
require_once '../util/functions.php';
header('Content-Type: application/json');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);

$world = 0;
$map = 0;
$letter = 0;
$maprank = 0;
$result = array();

if(isset($_GET['world']) && isset($_GET['map']) && is_numeric($_GET['world']) && is_numeric($_GET['map'])){
	$world = $conn->real_escape_string($_GET["world"]);
	$map = $conn->real_escape_string($_GET["map"]);
}
else
	error(400, "Invald world or map");

if(isset($_GET['node'])){
	if(preg_match('/^[A-z]$/', $_GET['node']) != 1) error(400, "Node must be a single letter!");
	$letter = $conn->real_escape_string(strtoupper($_GET["node"]));
}
if(isset($_GET['maprank']) && $world > 6){
	if(!is_numeric($_GET['maprank'])) error(400, "Invalid maprank");
	$maprank = $conn->real_escape_string($_GET["maprank"]);
}
$nodesql = "SELECT * FROM kancolle.db_node_counts main 
			INNER JOIN kancolledb.nodes sub ON main.world=sub.world AND main.map=sub.map AND main.node=sub.id
			WHERE main.world=$world AND main.map=$map AND (CASE WHEN  main.world > 6 THEN main.maprank=$maprank ELSE 1 END) AND main.node= ANY (SELECT id FROM kancolledb.nodes WHERE letter='$letter' AND world=main.world AND map=main.map)";

$msc = microtime(true);
$nodes = [];
$rs = $conn->query($nodesql);
if(!$rs) error(500, "Database Error");

$result[$letter] = array(
			"attempts" => 0,
			"drops" => array()
		);
while($nodedata = $rs->fetch_assoc()){
	$id = $nodedata['id'];
	$sql = "SELECT  main.world, main.map, main.node, main.maprank, main.result,
		COUNT(*) AS totalCount , 
		COUNT(CASE WHEN rank = 'S' THEN 1 END) AS Scount, 
		COUNT(CASE WHEN rank = 'A' THEN 1 END) AS Acount, 
		COUNT(CASE WHEN rank = 'B' THEN 1 END) AS Bcount 
		FROM (SELECT * FROM kancolle.db_ship_drop WHERE world=$world AND map=$map AND node=$id AND (CASE WHEN  world > 6 THEN maprank=$maprank ELSE 1 END)) main
		GROUP BY result, (CASE WHEN main.world > 6 THEN main.maprank END)
		ORDER BY result ASC, node ASC";
	
	$rs2 = $conn->query($sql);
	$result[$letter]["attempts"] += $nodedata['count'];
	while($row = $rs2->fetch_assoc()){
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
}
$msc = microtime(true)-$msc;
$response = array(
				"world"=>$world,
				"map"=>$map,
				"maprank" => $maprank,
				"node" => $letter,
				"queryTime"=> round($msc,3),
				"numResults"=>count($result),
				"data"=>$result
			);
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
?>
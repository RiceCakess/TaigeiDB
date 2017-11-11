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
	error(400,"Invalid world or map");

if(isset($_GET['maprank']) && $world > 6){
	if(!is_numeric($_GET['maprank']) || $_GET['maprank'] > 3) error(400,"Invalid maprank!");
	$maprank = $conn->real_escape_string($_GET["maprank"]);
}
$sql = "SELECT world, map, maprank, letter, SUM(Scount) AS Scount, SUM(Acount) AS Acount, SUM(Bcount) AS Bcount, SUM(success) AS success, result FROM
		(SELECT  main.world, main.map, main.node, main.maprank, main.result, info.letter,
				COUNT(*) AS success, 
				COUNT(CASE WHEN rank = 'S' THEN 1 END) AS Scount, 
				COUNT(CASE WHEN rank = 'A' THEN 1 END) AS Acount, 
				COUNT(CASE WHEN rank = 'B' THEN 1 END) AS Bcount 
				FROM (SELECT * FROM kancolle.db_ship_drop 
				WHERE world=$world AND map=$map AND (CASE WHEN  world > 6 THEN maprank=0 ELSE 1 END)) main
			INNER JOIN kancolle.db_node_counts sub 
				ON main.world=sub.world 
				AND main.map=sub.map 
				AND main.node=sub.node
				AND  (CASE WHEN main.world > 6 THEN main.maprank=sub.maprank ELSE 1 END)
			INNER JOIN kancolledb.nodes info
				ON main.world=info.world 
				AND main.map=info.map 
				AND main.node=info.id 
				GROUP BY node, result, (CASE WHEN main.world > 6 THEN main.maprank END)
				ORDER BY node ASC, result ASC) merge 
	GROUP BY world, map, letter, (CASE WHEN world > 6 THEN maprank END), result
	ORDER BY letter ASC, success DESC";

$nodesql = "SELECT *, SUM(count) as count FROM kancolle.db_node_counts main 
			INNER JOIN kancolledb.nodes sub ON main.world=sub.world AND main.map=sub.map AND main.node=sub.id
			WHERE main.world=$world AND main.map=$map AND (CASE WHEN  main.world > 6 THEN main.maprank=0 ELSE 1 END) GROUP BY letter";
//echo $nodesql;	
$msc = microtime(true);
$nodes = [];

$rs = $conn->query($nodesql);
if(!$rs) error(500,"Database Error");
while($row = $rs->fetch_assoc()){
	//$data = array("count"=>$row['count'], "letter"=>$row['letter'], "added"=>false);
	$nodes[$row['world'] . "-" . $row['map'] . "-" . $row['letter']] = $row['count'];
}

$rs = $conn->query($sql);
if(!$rs) error(500,"Database Error");

while($row = $rs->fetch_assoc()){
	$letter = $row['letter'];
	$count = $nodes[$row['world'] . "-" . $row['map'] . "-" . $letter];
	if(!array_key_exists($letter,$result)){
		$result[$letter]["attempts"] = $count;
	}
	$obj = [
		"result"=>$row["result"],
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
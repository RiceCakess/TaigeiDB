<?php 
require '../util/config.php';
require '../util/functions.php';
header('Content-Type: application/json');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $dataDB);

$shipid = 0;
$limit = $conn->real_escape_string(getLimit(1000,-1));
if(isset($_GET['id']) && is_numeric($_GET['id']))
	$shipid = $conn->real_escape_string($_GET["id"]);
else
	error(400,"Invalid Ship ID");
$sql = "SELECT  main.world, main.map, main.node, main.maprank, sub.count, info.letter,
COUNT(*) AS totalCount , 
COUNT(CASE WHEN rank = 'S' THEN 1 END) AS Scount , 
COUNT(CASE WHEN rank = 'A' THEN 1 END) AS Acount , 
COUNT(CASE WHEN rank = 'B' THEN 1 END) AS Bcount 
FROM (SELECT * FROM kancolle.db_ship_drop WHERE (world <= 6 || world = $current_event)AND result = $shipid) main
INNER JOIN kancolle.db_node_counts sub 
	ON main.world=sub.world 
	AND main.map=sub.map 
	AND main.node=sub.node 
	AND  (CASE WHEN main.world > 6 THEN main.maprank=sub.maprank ELSE 1 END)
INNER JOIN kancolledb.nodes info
	ON main.world=info.world 
	AND main.map=info.map 
	AND main.node=info.id 
GROUP BY main.world, main.map, main.node, (CASE WHEN main.world > 6 THEN main.maprank END)
ORDER BY totalCount DESC, main.world ASC, main.map ASC, main.node ASC
LIMIT $limit";
$msc = microtime(true);
$rs = $conn->query($sql);
if(!$rs) error(500, "Database Error");
$msc = microtime(true)-$msc;

$result = array();
while($row = $rs->fetch_assoc()){
	$obj = [
			"world" => $row['world'],
			"map" => $row['map'],
			"node" => $row['letter'],
			"maprank" => $row['maprank'],
			"Srank" => $row['Scount'],
			"Arank" => $row['Acount'],
			"Brank" => $row['Bcount'],
			"count" => $row['totalCount'],
			"attempts" => $row['count']
		];
	$result[] = $obj;
}
$response = array(
				"id"=>$shipid,
				"queryTime"=> round($msc,3),
				"numResults"=>count($result),
				"data"=>$result
			);
echo json_encode($response, JSON_PRETTY_PRINT);
http_response_code(200);
?>
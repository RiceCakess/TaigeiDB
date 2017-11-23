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
$sql = "SELECT world, map, maprank, node, letter, SUM(Scount) AS Scount, SUM(Acount) AS Acount, SUM(Bcount) AS Bcount, SUM(success) AS success, SUM(count) AS count 
	FROM (SELECT main.world, main.map, main.node, main.maprank, sub.count, info.letter,
	COUNT(*) AS success, 
	COUNT(CASE WHEN rank = 'S' THEN 1 END) AS Scount , 
	COUNT(CASE WHEN rank = 'A' THEN 1 END) AS Acount , 
	COUNT(CASE WHEN rank = 'B' THEN 1 END) AS Bcount 
	FROM (SELECT * FROM opendb.db_ship_drop WHERE (world <= 6 || world = $current_event)AND result = $shipid) main
	LEFT JOIN opendb.db_node_counts sub 
		ON main.world=sub.world 
		AND main.map=sub.map 
		AND main.node=sub.node 
		AND (CASE WHEN main.world > 6 THEN main.maprank=sub.maprank ELSE 1 END)
	LEFT JOIN kancolledb.nodes info
		ON main.world=info.world 
		AND main.map=info.map 
		AND main.node=info.id 
	GROUP BY main.world, main.map, main.node, (CASE WHEN main.world > 6 THEN main.maprank END)) merge 
GROUP BY world, map, (CASE WHEN letter is NOT NULL THEN letter END), (CASE WHEN world > 6 THEN maprank END)
ORDER BY success DESC
LIMIT $limit";
//echo $sql;
$msc = microtime(true);
$rs = $conn->query($sql);
$conn->close();
if(!$rs) error(500, "Database Error");
$msc = microtime(true)-$msc;

$result = array();
while($row = $rs->fetch_assoc()){
	$node = $row['letter'];
	if(!$row['letter'])
		$node = "Node_" . $row['node'];
	$obj = [
			"world" => $row['world'],
			"map" => $row['map'],
			"node" => $node,
			"maprank" => $row['maprank'],
			"Srank" => $row['Scount'],
			"Arank" => $row['Acount'],
			"Brank" => $row['Bcount'],
			"success" => $row['success'],
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
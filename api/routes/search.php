<?php
require '../util/config.php';
require '../util/functions.php';
header('Content-Type: application/json');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $infoDB);

$search;
$limit =  $conn->real_escape_string(getLimit(5,20));
$category = "all";
$result = array();
if(isset($_GET['query']) && strlen($_GET['query']) > 0)
	$search = $conn->real_escape_string($_GET["query"]);
else
	error(400,"Search query cannot be empty");

if(isset($_GET['category'])){
	if($_GET['category'] === "ship" || $_GET['category'] === "equip")
		$category = $conn->real_escape_string($_GET['category']);
	else
		error(400,"Invalid Category, please select \"ship\" or \"equip\"");
}
//Ship Search
if($category === "ship" || $category == "all"){
	$sql = "SELECT main.en_us, main.id, main.asset, sub.alias as type FROM ships main
			INNER JOIN shipTypes sub
				ON main.type=sub.id
			WHERE `prev` = 0 AND (main.en_us LIKE '$search%' OR main.en_us LIKE '% $search%') 
			ORDER BY CHAR_LENGTH(main.en_us) ASC LIMIT $limit";
	$rs = $conn->query($sql);
	if(!$rs) error(500,"Database Error");
	while($row = $rs->fetch_assoc()){
		$search_obj = [
				"category" => "ship",
				"subtype" => $row["type"],
				"name" => $row["en_us"],
				"id" => $row["id"],
				"asset" => $row['asset']
			];
			
		$result[] = $search_obj;
	}
}
//Equip Search
if($category === "equip" || $category == "all"){
	$split = array_slice(explode(" ", $search),0,3);
	$search_terms = "(main.en_us LIKE '" . $split[0] . "%' OR main.en_us LIKE '% " .  $split[0] . "%')";
	for($i = 1; $i < count($split); $i++){
		$search_terms .= " AND (main.en_us LIKE '%" . $split[$i] . "%')";
	}
	$sql = "SELECT main.en_us, main.id, sub.en_us as type FROM equips main 
			INNER JOIN equipTypes sub
				ON main.type = sub.id
			WHERE " . $search_terms . " ORDER BY CHAR_LENGTH(main.en_us) LIMIT $limit ";
	$rs = $conn->query($sql);
	if(!$rs) error(500,"Database Error");
	while($row = $rs->fetch_assoc()){
		$search_obj = [
			"category" => "equip",
			"type" => $row["type"],
			"name" => $row["en_us"],
			"id" => $row["id"]
		];
		
		$result[] = $search_obj;
	}
}

$result = array_splice($result,0,$limit);
$response = array(
				"query"=>$search,
				"numResults"=>count($result),
				"data"=>$result
			);
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
?>



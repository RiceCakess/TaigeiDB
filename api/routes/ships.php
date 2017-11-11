<?php
require '../util/config.php';
require '../util/functions.php';
header('Content-Type: application/json; charset=utf-8');
$conn = new mysqli($DBServer, $DBUser, $DBPass, $infoDB);
mysqli_set_charset($conn,"utf8");

$id;
$result = array();

if(isset($_GET['id']) && is_numeric($_GET['id']))
	$id = $conn->real_escape_string($_GET["id"]);
else
	error(400,"Invalid ID");

//Ship Search
$sql = "SELECT *,TRIM(CONCAT(shp.en_us,' ',sfx.en_us)) as en_us,TRIM(CONCAT(shp.ja_jp,' ',sfx.ja_jp)) as ja_jp, sub.alias as type FROM `ships` shp
INNER JOIN suffix sfx ON shp.suffix = sfx.id
INNER JOIN shipTypes sub ON shp.type=sub.id
WHERE shp.id = $id";

$rs = $conn->query($sql);
if(!$rs) error(500,"Database Error");

$row = $rs->fetch_assoc();
$response = [
		"id"=>$id,
		"no"=>$row["no"],
		"ja_jp" => $row["ja_jp"],
		"en_us" => $row["en_us"],
		"type" => $row["type"],
		"wiki" => $row["wiki"],
		"buildtime" => $row["buildtime"],
		"rare" => $row["rare"],
		"next" => $row["next"],
		"prev" => $row["prev"],
		"asset" => $row['asset'],
		"exclusive" => $row['exclusive']
	];
http_response_code(200);
echo json_encode($response, JSON_PRETTY_PRINT);
?>
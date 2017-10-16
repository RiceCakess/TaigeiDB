<?php
$method = $_SERVER['REQUEST_METHOD'];
if(isset($_GET['request']) && $method === "GET"){
	$request = $_GET['request'];
	$routeDir = "routes/";
	switch($request){
		case "search":
			require $routeDir . "search.php";
			break;
		case "ship_drop": 
			require $routeDir . "ship_drop.php"; 
			break;
		case "ship_drop/location": 
			require $routeDir . "ship_drop_location.php"; 
			break;
		case "ship_dev": 
			require $routeDir . "ship_dev.php"; 
			break;
		case "equip_dev":
			require $routeDir . "equip_dev.php";
			break;
		default: http_response_code(404);
	}
	return;
}
http_response_code(404);
?>
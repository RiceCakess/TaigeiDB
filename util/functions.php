<?php

//check if limit is numeric and return limit or $max if limit greater, $max = -1 if unlimited
function getLimit($default,$max){
	if(isset($_GET['limit'])){
		if(!is_numeric($_GET['limit'])) { error(400,"Limit must be an integer"); }
		return ($_GET['limit'] < $max || $max < 0) ? $_GET["limit"] : $max;
	}
	return $default;
}
function error($code, $msg){
	http_response_code($code);
	echo json_encode(["error"=>$msg, "code"=>$code], JSON_PRETTY_PRINT);
	exit();
}
?>
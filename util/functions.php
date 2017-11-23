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
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
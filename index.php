<?php 
require 'util/config.php';
require 'util/functions.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
header('Content-type: text/html; charset=utf-8');
mysqli_set_charset($conn,"utf8");
$sql = "SELECT timestamp, source, GROUP_CONCAT(message SEPARATOR '|') message FROM updates WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 2 WEEK) GROUP BY UNIX_TIMESTAMP(timestamp) DIV 300, source ORDER BY timestamp DESC LIMIT 6";

$rs = $conn->query($sql);
$arr = array();
$dbRename = [
	"equipTypes" => "equip types",
	"shipTypes" => "ship types",
];
$ignore = ["nodes","suffix"];
while($row = $rs->fetch_assoc()){
	$source = $row['source'];
	$message = "";
	//var_dump($row);
	switch($row['source']){
		case "updateDB": 
			$source = "KanColle Updates"; 
			$split = explode("|",$row['message']);
			
			$db = array();
			foreach($split as $str){
				$split = explode(",",$str);
				$database = $conn->real_escape_string($split[0]);
				$id = $conn->real_escape_string($split[1]);
				if(in_array($database,$ignore)){
					continue;
				}
				if($database == "ships"){
					//db["ships"]
					$sql = "SELECT *, TRIM(CONCAT(shp.en_us,' ',sfx.en_us)) as en_us, TRIM(CONCAT(shp.ja_jp,' ',sfx.ja_jp)) as ja_jp FROM $database shp INNER JOIN suffix sfx ON shp.suffix = sfx.id WHERE shp.id=$id ";
					$rs2 = $conn->query($sql);
					if($rs2 && $rs2->num_rows > 0){
						$row2 = $rs2->fetch_assoc();
						if(array_key_exists($database,$db))
							$db[$database] .= $row2["en_us"] . ", ";
						else
							$db[$database] = $row2["en_us"] . ", ";
					}
				}
				else{
					$sql = "SELECT * FROM $database WHERE id=$id";
					$rs2 = $conn->query($sql);
					if($rs2 && $rs2->num_rows > 0){
						$row2 = $rs2->fetch_assoc();
						if(array_key_exists($database,$db))
							$db[$database] .= $row2["en_us"] . ", ";
						else
							$db[$database] = $row2["en_us"] . ", ";
					}
				}
			}
			
			foreach($db as $key => $value){
				$message .= "<span style='text-decoration: underline;'>Updated " . (array_key_exists($key,$dbRename) ? $dbRename[$key] : $key)  . "</span><br>" . substr($value,0,strlen($value) - 2) . "<br>";
			}
			break;
		case "importDB": 
			$source = "Database Updates"; 
			$message = "Pulled new drops from OpenDB";
			break;
		case "twitterUpdates": 
			$source = "Twitter Updates"; 
			$split = explode(",",$row['message']);
			
			$message = $split[0]; 
			if(strpos($split[1], '.jpg') !== false)
				$message .= '<img src="' . $split[1] . '">';
			else
				$message .= "<br><a href='" . $split[1] . "'>Read More</a>";
			break;
	}
	$arr[] = [
		"source"=>$source,
		"message"=>$message,
		"timestamp"=>$row['timestamp']
	];
}

$sql = "SELECT shp.en_us, shp.id, shp.asset, type.alias as typeShort, type.en_us as type 
FROM (SELECT DISTINCT result as id FROM opendb.db_ship_drop WHERE world = $current_event) drp
INNER JOIN kancolledb.ships shp ON drp.id=shp.id 
INNER JOIN kancolledb.shipTypes type ON shp.type=type.id
WHERE shp.exclusive = 3
ORDER BY type.id DESC, shp.id LIMIT 6";
$rs = $conn->query($sql);
$arr2 = array();
while($row = $rs->fetch_assoc()){
	$arr2[] = [
		"id" => $row['id'],
		"asset" => $row['asset'],
		"name" => $row['en_us'],
		"type" => $row['type'],
		"typeShort" => $row['typeShort']
	];
}
$json = json_encode($arr2);
?>

<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<title>Tagei - Home</title>
		<script>
		$(document).ready(function(){
			$json = <?php echo $json; ?>;
			$json.forEach(function(obj){
				
				$(".ship-list").append("<a href='ship?id=" + obj.id + "#drop'><li>" + createShipBanner(obj.asset, obj.name)[0].outerHTML +"</li></a>");
			});
			addCollapse();
		});
		</script>
	</head>
	<body>
		<?php include_once ('includes/navbar.php'); ?>
		<div class="content" style="background-color: white">
			<div class="search-section" onload="">
				<div class="overlay"></div>
				<div class="search-bar main-search">
					<input class="form-control form-control-lg" type="text" placeholder="Search...">
					<ul class="list-group search-dropdown" id="livesearch">
					</ul>
				</div>
			</div>
			<div class="container card-section">
			<!-- card decks? !-->
				<div class="row">
					<div class="col-md-6">
						<div class="card">
							<div class="card-header">Recent</div>
							<div class="card-body" style="min-height:450px;">
								<center><h3>Fall 2017 Event In Progress!</h3></center>
								<img style="max-width: 100%" src="assets/temp/Fall_2017_Event_Banner.gif">
								<strong>Showdown at Operation Shō-Gō! Battle of Leyte Gulf</strong>
								<hr style="margin-top: .5rem; margin-bottom: .5rem">
								<strong>Maps</strong>
								<ul class="nav nav-pills type-nav">
									<li class="nav-item">
										<a class="nav-link" href="drop?world=40&map=1">E-1</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="drop?world=40&map=2">E-2</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="drop?world=40&map=3">E-3</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="drop?world=40&map=4">E-4</a>
									</li>
								</ul>
								<hr style="margin-top: .5rem; margin-bottom: .5rem">
								<strong>Notable Drops</strong>
								<ul class="ship-list"></ul>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card update-card">
							<div class="card-header">Updates</div>
							<div class="card-block" style="padding: 0px">
								<ul class="list-group list-group-flush update-list">
								<!--<a class="twitter-timeline" data-height="400" href="https://twitter.com/KanColle_STAFF?ref_src=twsrc%5Etfw">Tweets by KanColle_STAFF</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> !-->
									<?php foreach($arr as $entry){ ?>
									<li class="list-group-item">
										<div class="update-list-header">
											<span class="source"><?php echo  $entry['source']; ?></span>
											<span class="timestamp"><?php echo time_elapsed_string( $entry["timestamp"]);?></span>
										</div>
										<?php echo $entry['message'] ?>
									</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>

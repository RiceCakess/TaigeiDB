<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
//$sql = "SELECT DISTINCT result, shp.en_us, shp.id, shp.asset, type.alias as typeShort, type.en_us as type FROM opendb.db_ship_drop drp INNER JOIN kancolledb.ships shp ON drp.result=shp.id INNER JOIN kancolledb.shipTypes type 
//ON shp.type=type.id WHERE (drp.world < 7 OR drp.world = $current_event) ORDER BY type.id ASC, shp.id ASC";
$sql = "SELECT shp.en_us, shp.id, shp.asset, type.alias as typeShort, type.en_us as type 
FROM  (SELECT id FROM kancolledb.ships WHERE droppable = 1
UNION
SELECT DISTINCT result as id FROM opendb.db_ship_drop WHERE world = $current_event) drp
INNER JOIN kancolledb.ships shp ON drp.id=shp.id 
INNER JOIN kancolledb.shipTypes type ON shp.type=type.id
ORDER BY type.id ASC, shp.id ASC";
$rs = $conn->query($sql);

$arr = array();
while($row = $rs->fetch_assoc()){
	$arr[] = [
		"id" => $row['id'],
		"asset" => $row['asset'],
		"name" => $row['en_us'],
		"type" => $row['type'],
		"typeShort" => $row['typeShort']
	];
}
$json = json_encode($arr);
//var_dump($arr);
?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<title>Tagei - Drop List</title>
		<script>
		$(document).ready(function(){
			$json = <?php echo $json; ?>;
			$json.forEach(function(obj){
				if(!$("#" + obj.typeShort).length){
					$card = $('<div class="card data-card category" id="' + obj.typeShort + '"></div>');
					$card.append('<div class="card-header">' + obj.type + '</div>');
					$card.append('<div class="card-block"><ul class="ship-list"></ul></div>');
					$(".row").append($card);
					
					$(".type-nav").append('<li class="nav-item"><a class="nav-link" href="#'+ obj.typeShort + '">' + obj.typeShort + '</a></li>');
				}
				
				$("#" + obj.typeShort + " > .card-block > ul").append("<a href='ship?id=" + obj.id + "#drop'><li>" + createShipBanner(obj.asset, obj.name)[0].outerHTML +"</li></a>");
			});
			addCollapse();
		});
		</script>
	</head>
	<body>
		<div class="content">
			<?php require_once ('includes/navbar.php');?>
			<div class="container full-page">
				<div class="row">
					<div class="page-header">Drop List</div>
					
					<ul class="nav nav-pills type-nav">
					</ul>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
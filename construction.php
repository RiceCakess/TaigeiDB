<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
$sql = "SELECT shp.en_us, shp.id, shp.asset, type.alias as typeShort, type.en_us as type
		FROM ships shp 
		INNER JOIN shipTypes type 
		ON shp.type=type.id WHERE craftable=1 
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
				
				$("#" + obj.typeShort + " > .card-block > ul").append("<a href='ship.php?id=" + obj.id + "'><li>" + createShipBanner(obj.asset, obj.name)[0].outerHTML +"</li></a>");
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
					<div class="page-header">Construction</div>
					
					<ul class="nav nav-pills type-nav">
					</ul>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
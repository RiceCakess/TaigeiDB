<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
$sql = "SELECT eqp.en_us, eqp.id, type.iconName as iconName, type.en_us as type, type.icon as icon
		FROM equips eqp 
		INNER JOIN equipTypes type 
		ON eqp.type=type.id WHERE craftable=1 
		ORDER BY type.id ASC, eqp.id ASC";
$rs = $conn->query($sql);

$arr = array();
while($row = $rs->fetch_assoc()){
	$arr[] = [
		"id" => $row['id'],
		"name" => $row['en_us'],
		"type" => $row['type'],
		"icon" => $row['icon'],
		"iconName" => $row['iconName'] ? $row['iconName'] : $row['type']
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
			//console.log($json);
			$json.forEach(function(obj){
				if(!$("#" + obj.icon).length){
					$card = $('<div class="card data-card category" id="' + obj.icon + '"></div>');
					$card.append('<div class="card-header">' + obj.type + '</div>');
					$card.append('<div class="card-block"><ul class="ship-list"></ul></div>');
					$(".row").append($card);
					var iconName = obj.iconName.toLowerCase();
					iconName = iconName.split(" ").join("_").split("-").join("_");
					
					$(".type-nav").append('<li class="nav-item"><a class="nav-link" href="#'+ obj.icon + '"><img src="'+ assetPath + "icons/plain/" + iconName +'.png"></a></li>');

				}
				
				$("#" + obj.icon + " > .card-block > ul").append("<a href='equip.php?id=" + obj.id + "'><li>" + createEquipBanner(obj.id,obj.name)[0].outerHTML + "</li></a>");
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
					<div class="page-header">Development</div>
					<ul class="nav nav-pills type-nav" id="equip">
					</ul>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
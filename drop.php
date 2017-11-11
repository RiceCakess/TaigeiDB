<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);

$world = $map = 0;
if(isset($_GET["world"]) && is_numeric($_GET["map"]) & isset($_GET["map"]) && is_numeric($_GET["map"])){
	$world = $_GET["world"];
	$map = $_GET['map'];
}
else{
	http_response_code(404);
	return;
}
$sql = "SELECT * FROM ships WHERE prev=0";

$rs = $conn->query($sql);

$arr = array();
while($row = $rs->fetch_assoc()){
	$arr[$row['id']] = $row;
}
//var_dump($arr);
?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<script>
		var world = <?php echo $world; ?>;
		var map = <?php echo $map; ?>;
		$(document).ready(function(){
			$.getJSON("assets/world/worlds.json", function(json) {
				var obj = json;
				if(obj.hasOwnProperty(world)){
					for(var mapID in obj[world].maps){
						var mapObj = obj[world].maps[mapID];
						if(mapID == map){
							$(".map-name").text(world + "-" + map + ": " + mapObj.en_us);
						}
						$(".info-col > .card > .card-header").text("World " + world + ": " +obj[world].en_us);
						$(".info-col > .card > .list-group").append('<a href="drop.php?world=' + world + '&map=' + mapID +'"><li class="list-group-item ' + (mapID==map ? "active" : "") + '">' + world + "-" + mapID + ": " + mapObj.en_us + "</li></a>");
					}
				}
			});
			$(".map-img").children("img").attr("src","assets/world/map/" + world + "-" + map + "_Map.jpg");
			
			//console.log("test");
			var fairy = addLoadingFairy(".data-col");
			$.get("api/ship_drop/location",{"world": world, "map": map}).done(function(res) {
				Object.keys(res.data).forEach(function(letter,index){
					var node = res.data[letter];
					var attempts = node.attempts;
					//console.log(letter);
					//console.log(node);
					if(world < 6 && attempts < 100){
						return;
					}
					$(".type-nav").append('<li class="nav-item"><a class="nav-link" href="#'+ letter + '">' + letter + '</a></li>');

					$(".nodes").append('<div class="card data-card" id="' + letter + '"><div class="card-header">' + letter + '</div><div class="card-block"></div></div>');
					$("#" + letter + " > .card-block").append('<table class="table drop-table"><thead><tr><th>Ship</th><th><span class="kcIcon Srank"></span></th><th><span class="kcIcon Arank"></span></th><th><span class="kcIcon Brank"></span></th><th>Success</th><th>Rate</th></tr></thead><tbody></tbody></table>');
					node.drops.forEach(function(drop, index2){
						//var drop = node.drops[i];
						var percent = (drop.success/attempts) *100;
						//console.log(world);
						if((world < 6 && attempts < 100)){
							return;
						}
						if(drop.result != 0)
							$.get("api/ships",{id: drop.result}).done(function(response) {
								//console.log()
								var row = $("<tr/>")
								.append("<td " + (response.exclusive > 0 ? 'class="rare"' : "") + ">" + response.en_us + "</td>")
								.append("<td>" + drop.Srank + "</td>")
								.append("<td>" + drop.Arank + "</td>")
								.append("<td>" + drop.Brank + "</td>")
								.append("<td>" + drop.success + "</td>")
								.append("<td>" + round(percent,4) +"%" + "</td>");
								if(drop.result != 0)
									$("#" + letter + " > .card-block > .drop-table > tbody").append(row);
							}).then(function(){
								//console.log(res.data.length + " " + node.drops.length);
								if(index == res.numResults - 1 && index2 == node.drops.length-1){
									$(fairy).remove();
									$(".nodes").show();
									var hash = window.location.hash.substr(1).toLowerCase();
									addCollapse(function(){
										if(hash === "")
											return;
										$(".nodes > .card").each(function(){
											console.log($(this).attr("id").toLowerCase());
											//console.log($(this).attr("id").toLowerCase() == hash);
											if($(this).attr("id").toLowerCase() !== hash)
												$(this).children(".card-block").collapse("hide");
										});
									});
									
								}
							});
					});
				});
			});
		});
		</script>
	</head>
	<body>
		<div class="content">
			<?php require_once ('includes/navbar.php');?>
			<div class="container full-page">
				<div class="row">
					<div class="col-lg-3 info-col">
						<div class="card">
							<div class="card-header">
								
							</div>
							<ul class="list-group list-group-flush">
							</ul>
						</div>
					</div>
					<div class="col-lg-9 data-col">
						<div class="page-header map-name"></div>
						<div class="map-img" style="text-align: center; margin: 10px;">
							<img src="">
						</div>
						<ul class="nav nav-pills type-nav">
						</ul>
						<div class="nodes" style="display: none;">
						
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
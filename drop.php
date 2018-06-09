<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);

$world = $map = $maprank = 0;
if(isset($_GET["world"]) && is_numeric($_GET["map"]) & isset($_GET["map"]) && is_numeric($_GET["map"])){
	$world = $_GET["world"];
	$map = $_GET['map'];
}
else{
	http_response_code(404);
	include('404.php'); 
	return;
}
if($world > 6)
	$maprank = 1;
if(isset($_GET['maprank']) && $world > 6 && is_numeric($_GET['maprank']) && $_GET['maprank'] <= 3 && $_GET['maprank'] > 0){ 
	$maprank = $conn->real_escape_string($_GET["maprank"]);
}

$rs = $conn->query("SELECT regis FROM opendb.db_ship_drop ORDER BY uid DESC LIMIT 1"); 
$row = $rs->fetch_assoc(); 
$lastupdate = $row["regis"];
//var_dump($arr);
?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<title></title>
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
						<div class="row">
							<div class="col-md-9">
								<label>Nodes:</label>
								<ul class="nav nav-pills">
								</ul>
							</div>
							<div class="col-md-3">
								<div class="form-group" <?php if($maprank == 0) echo "hidden"; ?>>
									<label for="diff">Difficulty</label>
									<select class="form-control" id="diff">
										<option <?php if($maprank == 1) echo "selected"?> value="1">Easy</option>
										<option <?php if($maprank == 2) echo "selected"?> value="2">Medium</option>
										<option <?php if($maprank == 3) echo "selected"?> value="3">Hard</option>
									</select>
								</div>
							</div>
						</div>
						<div class="nodes" style="display: none;">
						
						</div>
						
					</div>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
		<script>
		var world = <?php echo $world; ?>;
		var map = <?php echo $map; ?>;
		var maprank = <?php echo $maprank; ?>;
		$("title").append((world > 6 ? locNumber(world,map) : "World " + world + "-" + map) + " Drops");
		$(document).ready(function(){
			$("select#diff").on("change", function() {
				window.location.href = "drop?world=" + world + "&map=" + map + "&maprank=" + $(this).val();
			});
			$(".map-name").text(locNumber(world,map));
			$.getJSON("assets/world/worlds.json", function(json) {
				var obj = json;
				if(obj.hasOwnProperty(world)){
					for(var mapID in obj[world].maps){
						var mapObj = obj[world].maps[mapID];
						if(mapID == map){
							$(".map-name").append(": " + mapObj.en_us);
						}
						$(".info-col > .card > .card-header").text((world > 6 ? "" : "World " + world + ": ") + obj[world].en_us);
						$(".info-col > .card > .list-group").append('<a href="drop?world=' + world + '&map=' + mapID +'"><li class="list-group-item ' + (mapID==map ? "active" : "") + '">' + (world > 6 ? "E" : world) + "-" + mapID + ": " + mapObj.en_us + "</li></a>");
					}
				}
			});
			if(world > 6)
				$(".map-img").children("img").attr("src","assets/world/map/" + locNumber(world,map).split(" ").join("_") + "_Map.png");
			else
				$(".map-img").children("img").attr("src","assets/world/map/" + world + "-" + map + "_Map.jpg");
			
			var fairy = addLoadingFairy(".data-col");
			var p1 = [];
			$.get("api/ship_drop/location",{"world": world, "map": map, "maprank": maprank}).done(function(res) {
				console.log(res);
				if(res.numResults == 0){
					$(fairy).remove();
					$(".data-col > .row").hide();
					$(".data-col").append(createAlert("danger","No results on this map"));
					return;
				}
				Object.keys(res.data).forEach(function(letter,index){
					var node = res.data[letter];
					var attempts = node.attempts;
					var header = (letter.includes("Node") ? letter.replace("_", " ") : letter); 
					if((world <= 6 && attempts > 100) || world > 6 && node.drops.length > 1){
						$(".nav-pills").append('<li class="nav-item"><a class="nav-link" href="#'+ letter + '">' + header  + '</a></li>');
						$(".nodes").append('<div class="card data-card" id="' + letter + '"><div class="card-header">' + header + " - <span class='text-muted'>"+ attempts + ' attempts</span></div><div class="card-block"></div></div>');
						$("#" + letter + " > .card-block").append('<table class="table drop-table"><thead><tr><th>Ship</th><th><span class="kcIcon Srank"></span></th><th><span class="kcIcon Arank"></span></th><th><span class="kcIcon Brank"></span></th><th>Success</th><th>Rate</th></tr></thead><tbody></tbody></table>');
						
						node.drops.forEach(function(drop, index2){
							
							var percent = (drop.success/attempts) *100;
							if(drop.result != 0)
								var row = $("<tr/>")
								.append("<td><a " + (drop.ship.exclusive > 0 ? 'class="rare"' : "") + " href='ship?id=" + drop.ship.id + "'>" + drop.ship.en_us + "</a></td>")
								.append("<td>" + drop.Srank + "</td>")
								.append("<td>" + drop.Arank + "</td>")
								.append("<td>" + drop.Brank + "</td>")
								.append("<td>" + drop.success + "</td>")
								.append("<td>" + round(percent,4) +"%" + "</td>");
								if(drop.ship.id != 0 && drop.ship.id)
									$("#" + letter + " > .card-block > .drop-table > tbody").append(row);
						});
					}
					//After functon finishes
					if(index == Object.keys(res.data).length - 1){
						$(".nav-pills").append('<li class="nav-item"><a class="nav-link" href="#all">Show all</a></li>');
						Promise.all(p1).then(function(){
							$(fairy).remove();
							$(".nodes").show();
							$(".nodes").append('<div class="text-muted">Data last updated on: <?php echo $lastupdate; ?></div>');
							var hash = window.location.hash.substr(1).toLowerCase();
							addCollapse(function(){
								if(hash === "")
									return;
								$(".nodes > .card").each(function(){
									if($(this).attr("id").toLowerCase() !== hash)
										$(this).children(".card-block").collapse("hide");
								});
							});
							addSort();
							$(".nav-pills > .nav-item > .nav-link").click(function(e){
								e.preventDefault();
								var btnhash = $(this).attr("href").replace("#","");
								var count = $(".nodes > .card").length;
								$(".nodes > .card").each(function(){
									if($(this).attr("id").toLowerCase() === btnhash.toLowerCase() || btnhash.toLowerCase() === "all")
										$(this).children(".card-block").collapse("show");
									else
										$(this).children(".card-block").collapse("hide");
								});
							});
						});
					}
				});
			});
		});
		</script>
	</body>
</html>
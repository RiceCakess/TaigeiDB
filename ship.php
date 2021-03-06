<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
$conn->set_charset("utf8");
$assetPath = "assets/KanColleAssets/";
$id = 0;
if(isset($_GET["id"]) && is_numeric($_GET["id"])){
	$id = $_GET["id"];
}
else{
	http_response_code(404);
	include('404.php'); 
	return;
}

$sql = "SELECT shp.*, typ.en_us type FROM ships shp INNER JOIN shipTypes typ ON shp.type = typ.id WHERE shp.id=" . $conn->real_escape_string($id);
$rs = $conn->query($sql);
$no = $asset = $name = $type = $suffix = $wiki = 0;
if(!$rs || $rs->num_rows <= 0){
	http_response_code(404);
	include('404.php'); 
	return;
}
$shipInfo = $rs->fetch_assoc();
/*$no = $row["no"];
$asset = $row["asset"];
$name = $row['en_us'];
$suffix = $row['suffix'];
$wiki = $row['wiki'];
$buildtime = $row['buildtime'];*/
?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<title><?php echo $shipInfo["en_us"]; ?> Construction/Drop</title>
	</head>
	<body>
		
		<div class="content">
			<?php require_once ('includes/navbar.php');?>
			<div class="container full-page">
				<!--<img class="ship-bg" src="<?php echo $assetPath . "ships/" . $shipInfo["asset"] . "/17.png" ?>"/>!-->
				<div class="ship-no">No. <?php echo $shipInfo["no"] ?></div>
				<div class="row">	
					<div class="col-lg-3 info-col">
						<center>
							<div class="card ship-card">
								<img class="card-img-top" src="<?php echo $assetPath . "ships/" . $shipInfo["asset"] . "/5.png" ?>">
								<div class="card-footer">
									<?php echo $shipInfo["en_us"] . " (" . $shipInfo["ja_jp"] . ")"; ?>
									<br>
									<?php echo $shipInfo["type"]; ?> - <a href="<?php echo $shipInfo["wiki"]; ?>">Wikia</a>
								</div>
							</div>
						</center>
					</div>
					<div class="col-lg-9 data-col">
						<ul class="nav nav-tabs tabs-nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#construction">Construction</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#drop" id="#drop">Drop</a>
							</li>
						</ul>
						<div class="tab-content">
							<div id="construction" class="tab-pane active" role="tabpanel">
							  
								<div class="card data-card hidden card-collapse" id="dev">
									<div class="card-header">
										Construction
									</div>
									<div class="card-block">
										<table class="table construction-table">
											<thead>
												<tr>
													<th><span class="kcIcon fuel" title="Fuel"></span></th>
													<th><span class="kcIcon ammo" title="Ammo"></span></th>
													<th><span class="kcIcon steel" title="Steel"></span></th>
													<th><span class="kcIcon bauxite" title="Bauxite"></span></th>
													<th><span class="kcIcon material" title="Material"></span></th>
													<th title="Number of successes">Success</th>
													<th title="Number of attempts">Attempts</th>
													<th title="Percent">%</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="card data-card hidden card-collapse" id="lsc">
									<div class="card-header">
										Large Scale Construction
									</div>
									<div class="card-block">
										<table class="table construction-table">
											<thead>
												<tr>
													<th><span class="kcIcon fuel" title="Fuel"></span></th>
													<th><span class="kcIcon ammo" title="Ammo"></span></th>
													<th><span class="kcIcon steel" title="Steel"></span></th>
													<th><span class="kcIcon bauxite" title="Bauxite"></span></th>
													<th><span class="kcIcon material" title="Material"></span></th>
													<th title="Number of successes">Success</th>
													<th title="Number of attempts">Attempts</th>
													<th title="Percent">%</th>
												</tr>
											</thead>
											<tbody>
											
											</tbody>
										</table>
									</div>
								</div>
								<div class="card data-card hidden card-collapse" id="flagship">
									<div class="card-header">
										Frequent Flagship
									</div>
									<div class="card-block">
										<table class="table construction-table">
											<thead>
												<tr>
													<th>Ship</th>
													<th>Type</th>
													<th># times used</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
							</div>
							<div id="drop" class="tab-pane" role="tabpanel">
							<div class="card data-card hidden card-collapse" id="eventdrop">
									<div class="card-header">
										Current Event Drop
									</div>
									<div class="card-block">
										<table class="table drop-table">
											<thead>
												<tr>
													<th>Location</th>
													<th><span class="kcIcon Srank"></span></th>
													<th><span class="kcIcon Arank"></span></th>
													<th><span class="kcIcon Brank"></span></th>
													<th>Attempts</th>
													<th>Rate</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<div class="card data-card hidden card-collapse" id="drop">
									<div class="card-header">
										Drops
									</div>
									<div class="card-block">
										<table class="table drop-table">
											<thead>
												<tr>
													<th>Location</th>
													<th><span class="kcIcon Srank"></span></th>
													<th><span class="kcIcon Arank"></span></th>
													<th><span class="kcIcon Brank"></span></th>
													<th>Attempts</th>
													<th>Rate</th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include_once ('includes/footer.php'); ?>
		<script>
		var current_event = <?php echo $current_event; ?>;
		$(document).ready(function(){
			//prevent anchor jump to middle of page
			if (location.hash) {
			  setTimeout(function() {
				window.scrollTo(0, 0);
			  }, 1);
			}
			var anchor = window.location.hash;
			var dropBuilt = false;
			var constrBuilt = false;
			var hash = window.location.hash.substr(1).toLowerCase();
			
			if(hash === "drop"){
				fetchDropTable();
				$("a[href='#drop']").tab('show');
			}
			else{
				fetchConstructionData();
				constrBuilt = true;
			}
			
			$("a[href='#drop']").click(function(){
				if(!dropBuilt){
					fetchDropTable();
					dropBuilt = true;
				}
			});
			$("a[href='#construction']").click(function(){
				if(!constrBuilt){
					fetchConstructionData();
					constrBuilt = true;
				}
			});
		});
		function fetchConstructionData(){
			var fairy = addLoadingFairy("#construction");
			var show = [];
			var p1 = $.get("api/ship_dev",{id: <?php echo $id ?>, limit:8, lsc:false}).done(function(res) {
				var devcard = $(".data-card#dev");
				var tableBody = devcard.find("tbody");
				if(res.data.length == 0){
					return;
				}
				appendData(tableBody,res.data);
				show.push(devcard);
			});
			//Check for LSC
			var p2 = $.get("api/ship_dev",{id: <?php echo $id ?>, limit:8, lsc:true}).done(function(res) {
				var devcard = $(".data-card#lsc");
				var tableBody = devcard.find("tbody");
				if(res.data.length == 0){
					return;
				}
				appendData(tableBody,res.data);
				show.push(devcard);
			});
			//check for both, and show flagship
			Promise.all([p1, p2]).then(function(){
				if(show.length == 0){
					$(fairy).remove();
					$("#construction").prepend(
					createAlert("danger","This ship is currently not constructable!"));
					return;
				}
				$.get("api/ship_dev/flagship",{id: <?php echo $id ?>, limit:5}).done(function(res) {
					var devcard = $(".data-card#flagship");
					var tableBody = devcard.find("tbody");
					var promises = [];
					var arr = [];
					res.data.forEach(function(obj){
						promises.push($.get("api/ships",{id: obj.flagship}).done(function(response) {
							arr.push({
								ship: response,
								db: obj
							});
						}));
					});
					Promise.all(promises).then(function(){
						arr.sort(function(a,b){
							return b.db.count - a.db.count;
						});
						arr.forEach(function(obj){
							var row = $("<tr/>")
								.append("<td>" + createShipBanner(obj.ship.asset, obj.ship.en_us, obj.ship.type)[0].outerHTML + "</td>")
								.append("<td>" + obj.ship.type + "</td>")
								.append("<td>" + obj.db.count + "</td>");
							tableBody.append(row);
						});
						
						$(fairy).remove();
						for(var i = 0; i < show.length; i++){
							$(show[i]).removeClass("hidden");
						}
						devcard.removeClass("hidden");
					});
				});
			});
		}
		function appendData(table,data){
			data.forEach(function(obj){
				percent = (obj.count/obj.attempts) *100;
				var row = $("<tr/>")
					.append("<td>" + obj.fuel + "</td>")
					.append("<td>" + obj.ammo + "</td>")
					.append("<td>" + obj.steel + "</td>")
					.append("<td>" + obj.bauxite + "</td>")
					.append("<td>" + obj.material + "</td>")
					.append("<td>" + obj.count + "</td>")
					.append("<td>" + obj.attempts + "</td>")
					.append("<td>" + round(percent,4) +"%" + "</td>");
				table.append(row);
			});
		}
		var difficulty = ["","E","N","H"];
		function fetchDropTable(){
			var fairy = addLoadingFairy("#drop");
			$.get("api/ship_drop",{id: <?php echo $id ?>}).done(function(res){
				$(fairy).remove();
				var devcard = $(".data-card#drop");
				var eventcard = $(".data-card#eventdrop");
				var tableBody = devcard.find("tbody");
				var eventBody = eventcard.find("tbody");
				//count results not filtered
				var resCount = [0,0];
				console.log(res);
				res.data.forEach(function(obj){
					//if normal map has less than 10 reports, then ignore or event map doesn't have difficulty
					if(obj.count < 10 && obj.world <=6 || (obj.maprank == 0 && obj.world > 6)){
						return;
					}
					percent = (obj.success/obj.attempts) *100;
					var row = $("<tr/>")
						.append("<td><a href='drop?world=" 
						+ obj.world + "&map=" + obj.map + ((obj.world == current_event) ? "&maprank=" + obj.maprank : "" )+ "#" + obj.node + "'>" 
						+ ((obj.world == current_event) ? ("E-" + obj.map) : (obj.world + "-" + obj.map)) 
						+ difficulty[obj.maprank] + " " + obj.node + "</a></td>")
						.append("<td>" + obj.Srank + "</td>")
						.append("<td>" + obj.Arank + "</td>")
						.append("<td>" + obj.Brank + "</td>")
						.append("<td>" + obj.attempts + "</td>")
						.append("<td>" + round(percent,4) +"%" + "</td>");

					if(obj.world > 6){
						eventBody.append(row);
						resCount[1]++;
					}
					else{
						tableBody.append(row);
						resCount[0]++;
					}
				});
				if(resCount[0] + resCount[1] == 0){
					$("#drop").prepend(
						createAlert("danger","This ship does not drop!"));
					return;
				}
				if(resCount[0] > 0)
					devcard.removeClass("hidden");
				if(resCount[1] > 0)
					eventcard.removeClass("hidden");
			});
		}
		</script>
	</body>
	
</html>
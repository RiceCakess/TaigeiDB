<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
$assetPath = "assets/KanColleAssets/";
$id = 0;
if(isset($_GET["id"]) && is_numeric($_GET["id"])){
	$id = $_GET["id"];
}
else{
	http_response_code(404);
	return;
}

$sql = "SELECT * FROM ships WHERE id=" . $conn->real_escape_string($id);
$rs = $conn->query($sql);
$no = $asset = $name = $type = $suffix = $wiki = 0;
if(!$rs){
	http_response_code(404);
	return;
}
$row = $rs->fetch_assoc();
$no = $row["no"];
$asset = $row["asset"];
$name = $row['en_us'];
$suffix = $row['suffix'];
$wiki = $row['wiki'];
$buildtime = $row['buildtime'];
?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<script>
		$(document).ready(function(){
			var anchor = window.location.hash;
			var dropBuilt = false;
			fetchConstructionData();
			$("a[href='#drop']").click(function(){
				if(!dropBuilt){
					fetchDropTable();
					dropBuilt = true;
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
							//devcard.find(".card-block").append(createShipBanner(obj.ship.asset, obj.ship.en_us, obj.ship.type));
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
				var tableBody = devcard.find("tbody");
				
				//count results not filtered
				var resCount = 0;
				res.data.forEach(function(obj){
					//if normal map has less than 10 reports, then ignore
					if(obj.count < 10 && obj.map <=6){
						return;
					}
					resCount++;
					//console.log(obj);
					percent = (obj.success/obj.attempts) *100;
					var row = $("<tr/>")
						.append("<td>" + obj.world + "-" + obj.map + difficulty[obj.maprank] + " " + obj.node + "</td>")
						.append("<td>" + obj.Srank + "</td>")
						.append("<td>" + obj.Arank + "</td>")
						.append("<td>" + obj.Brank + "</td>")
						.append("<td>" + obj.attempts + "</td>")
						.append("<td>" + round(percent,4) +"%" + "</td>");
					tableBody.append(row);
				});
				if(resCount == 0){
					$("#drop").prepend(
						createAlert("danger","This ship does not drop!"));
					return;
				}
				devcard.removeClass("hidden");
			});
		}
		</script>
	</head>
	<body>
		
		<div class="content">
			<?php require_once ('includes/navbar.php');?>
			<div class="container full-page">
				<!--<img class="ship-bg" src="<?php echo $assetPath . "ships/" . $asset . "/17.png" ?>"/>!-->
				<div class="ship-no">No. <?php echo $no ?></div>
				<div class="row">	
					<div class="col-lg-3 info-col">
						<center>
							<div class="card ship-card">
								<img class="card-img-top" src="<?php echo $assetPath . "ships/" . $asset . "/5.png" ?>">
								<div class="card-footer">
									<?php echo $name; ?>
									<br>
									<a href="<?php echo $wiki; ?>">Wiki</a>
								</div>
							</div>
						</center>
					</div>
					<div class="col-lg-9 data-col">
						<ul class="nav nav-tabs ship-data-nav" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" data-toggle="tab" href="#construction">Construction</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" href="#drop">Drop</a>
							</li>
						</ul>
						<div class="tab-content">
							<div id="construction" class="tab-pane active" role="tabpanel">
								<div class="card data-card hidden" id="dev">
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
								<div class="card data-card hidden" id="lsc">
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
								<div class="card data-card hidden" id="flagship">
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
								<div class="card data-card hidden" id="drop">
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
	</body>
	
</html>
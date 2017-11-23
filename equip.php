<?php
require 'util/config.php';
$conn = mysqli_connect($DBServer, $DBUser, $DBPass, $infoDB);
$assetPath = "assets/KanColleAssets/";
$id = 0;
if(isset($_GET["id"])){
	$id = $_GET["id"];
}

$sql = "SELECT * FROM equips WHERE id=" . $conn->real_escape_string($id);
$rs = $conn->query($sql);
$no = $asset = $name = $type = $suffix = $wiki = 0;
if(!$rs || $rs->num_rows <= 0){
	http_response_code(404);
	include('404.php'); 
	return;
}
$row = $rs->fetch_assoc();
$name = $row['en_us'];
$wiki = $row['wiki'];
$craftable = $row['craftable'];
?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<title><?php echo $name; ?>  Development</title>
		<script>
		$(document).ready(function(){
			fetchConstructionData();
		});
		function fetchConstructionData(){
			if(<?php echo $craftable; ?> == 0){
				$("#development").prepend(
				createAlert("danger","This equipment is currently not constructable!"));
				return;
			}
			var fairy = addLoadingFairy("#development");
			$.get("api/equip_dev",{id: <?php echo $id ?>, limit:8}).done(function(res) {
				var devcard = $(".data-card#dev");
				var tableBody = devcard.find("tbody");
				if(res.data.length == 0){
					return;
				}
				res.data.forEach(function(obj){
					percent = (obj.count/obj.attempts) *100;
					var row = $("<tr/>")
						.append("<td>" + obj.fuel + "</td>")
						.append("<td>" + obj.ammo + "</td>")
						.append("<td>" + obj.steel + "</td>")
						.append("<td>" + obj.bauxite + "</td>")
						.append("<td>" + obj.count + "</td>")
						.append("<td>" + obj.attempts + "</td>")
						.append("<td>" + round(percent,4) +"%" + "</td>");
						tableBody.append(row);
				});
			}).then(function(){
				$.get("api/equip_dev/flagship",{id: <?php echo $id ?>, limit:5}).done(function(res) {
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
						$(".data-card#dev").removeClass("hidden");
						devcard.removeClass("hidden");
					});
				});
			});
		}
		</script>
	</head>
	<body>
		
		<div class="content">
			<?php require_once ('includes/navbar.php');?>
			<div class="container full-page">
				<div class="row">	
					<div class="col-lg-3 info-col">
						<center>
							<div class="card ship-card">
								<img class="card-img-top" src="<?php echo $assetPath . "slotitem/card/" . sprintf("%03d", $id) . ".png" ?>">
								<div class="card-footer">
									<?php echo $name; ?>
									<br>
									<a href="<?php echo $wiki; ?>">Wiki</a>
								</div>
							</div>
						</center>
					</div>
					<div class="col-lg-9 data-col">
						<div class="tab-content">
							<div id="development" class="tab-pane active" role="tabpanel">
								<div class="card data-card hidden" id="dev">
									<div class="card-header">
										Development
									</div>
									<div class="card-block">
										<table class="table construction-table">
											<thead>
												<tr>
													<th><span class="kcIcon fuel" title="Fuel"></span></th>
													<th><span class="kcIcon ammo" title="Ammo"></span></th>
													<th><span class="kcIcon steel" title="Steel"></span></th>
													<th><span class="kcIcon bauxite" title="Bauxite"></span></th>
													<th title="Number of successes">Success</th>
													<th title="Number of attempts">Attempts</th>
													<th title="Percent">%</th>
												</tr>
											</thead>
											<tbody></tbody>
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
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
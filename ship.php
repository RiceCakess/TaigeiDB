<?php
$assetPath = "assets/KanColleAssets/";
require_once ('util/info_db.php');
$id = 0;
if(isset($_GET["id"])){
	$id = $_GET["id"];
}

$sql = "SELECT * FROM ships WHERE id=" . $infoConn->real_escape_string($id);
$rs = $infoConn->query($sql);
$no = $asset = $name = $type = $suffix = $wiki = 0;
if($rs){
	$row = $rs->fetch_assoc();
	$no = $row["no"];
	$asset = $row["asset"];
}

?>
<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<script src="js/common.js"></script>
		<script>
			$(document).ready(function(){
				var dropBuilt = false;
				fetchConstructionData();
				$("a[href='#drop']").click(function(){
					if(!dropBuilt){
						buildDropTable();
						dropBuilt = true;
					}
				});
			});
		function fetchConstructionData(){
			var fairy = addLoadingFairy("#construction");
			$.get("api/ship_dev",{id: <?php echo $id ?>, limit:10}).done(function(res) {
				$(fairy).remove();
				var table = $(".construction-table > tbody");
				if(res.data.length == 0){
					var alert = $("<div/>")
								.addClass("alert alert-danger")
								.attr("role","alert")
								.text("This ship is currently not constructable!");
					$("#construction").prepend(alert);
					return;
				}
				res.data.forEach(function(obj){
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
				$(".construction-table").show();
			});
		}
		var difficulty = ["","E","N","H"];
		function buildDropTable(){
			var fairy = addLoadingFairy("#drop");
			$.get("api/ship_drop",{id: <?php echo $id ?>}).done(function(res){
				$(fairy).remove();
				var table = $(".drop-table > tbody");
				if(res.data.length == 0){
					var alert = $("<div/>")
								.addClass("alert alert-danger")
								.attr("role","alert")
								.text("This ship does not drop!");
					$("#drop").prepend(alert);
					return;
				}
				res.data.forEach(function(obj){
					//if normal map has less than 10 reports, then ignore
					if(obj.count < 10 && obj.map <=6){
						return;
					}
					//console.log(obj);
					percent = (obj.count/obj.attempts) *100;
					var row = $("<tr/>")
						.append("<td>" + obj.world + "-" + obj.map + difficulty[obj.maprank] + " " + obj.node + "</td>")
						.append("<td>" + obj.Srank + "</td>")
						.append("<td>" + obj.Arank + "</td>")
						.append("<td>" + obj.Brank + "</td>")
						.append("<td>" + obj.count + "</td>")
						.append("<td>" + round(percent,4) +"%" + "</td>");
					table.append(row);
				});
				$(".drop-table").show();
			});
		}
		function addLoadingFairy(elm){
			var fairy = $("<div/>")
						.addClass("loadingFairy")
						.append("<img src='" + assetPath + "slotitem/item_character/" + pad(Math.floor(Math.random()*255),3) + ".png'/>")
						.append("<div>Loading...</div>");
			$(elm).append(fairy);
			return fairy;
		}
		</script>
	</head>
	<body>
		<?php require_once ('includes/navbar.php');?>
		<div class="content">
			<div class="container">
				<img class="ship-bg" src="<?php echo $assetPath . "ships/" . $asset . "/17.png" ?>"/>
				<div class="ship-no">No. <?php echo $no ?></div>
				<div class="row">	
					<div class="col-lg-3 ship-info-col">
						<div class="ship-img-container">
							<img src="<?php echo $assetPath . "ships/" . $asset . "/5.png" ?>">
						</div>
					</div>
					<div class="col-lg-9 ship-data-col">
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
								<table class="table table-responsive construction-table table-striped" style="display: none;">
									<thead>
										<tr>
											<th><span class="kcIcon fuel"></span></th>
											<th><span class="kcIcon ammo"></span></th>
											<th><span class="kcIcon steel"></span></th>
											<th><span class="kcIcon bauxite"></span></th>
											<th><span class="kcIcon material"></span></th>
											<th>Success</th>
											<th>Attempts</th>
											<th>Rate</th>
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
							</div>
							<div id="drop" class="tab-pane" role="tabpanel">
								<table class="table table-responsive drop-table table-striped" style="display: none;">
									<thead>
										<tr>
											<th>Location</th>
											<th><span class="kcIcon Srank"></span></th>
											<th><span class="kcIcon Arank"></span></th>
											<th><span class="kcIcon Brank"></span></th>
											<th>Success</th>
											<th>Rate</th>
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
	
</html>
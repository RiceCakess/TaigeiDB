<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<script>
		$(document).ready(function(){
			$.getJSON("assets/world/worlds.json", function(json) {
				var obj = json;
				var worlds = new Array();
				Object.keys(obj).forEach(function(worldID){
					var world = obj[worldID];
					$(".type-nav").append('<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#world' + worldID + '">World ' + worldID + '</a></li>')
					$(".tab-content").append('<div class="tab-pane" id="world' + worldID + '" role="tabpanel"><div class="card world-card"><div class="card-header">' + world.en_us + '</div><div class="card-block"><div class="world"></div></div></div></div></div>');
					var maps = new Array();
					Object.keys(world.maps).forEach(function(mapID){
						var worldBody = $(".tab-pane#world" + worldID).find(".world");
						worldBody.append('<a href="drop.php?world=' + worldID + '&map=' + mapID + '" class="' + (mapID > 4 ? "eo-" : "") + 'map"><img src="assets/world/banner/' + worldID + '-' + mapID + '_Banner.png"></a>');
					});
				});
				$("a[href='#world1']").tab('show');
			});
		});
		
		</script>
	</head>
	<body>
		<div class="content">
			<?php require_once ('includes/navbar.php');?>
			<div class="container full-page">
				<div class="row" style="align-content: flex-start;">	
					<div class="page-header">Drops</div>
					<ul class="nav nav-pills type-nav" role="tablist">
					</ul>
					<div class="tab-content" style="width:100%">
					</div>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
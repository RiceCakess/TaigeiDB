<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<title>Tagei - Worlds</title>
		<script>
		//var current_event = <?php echo $current_event; ?>;
		$(document).ready(function(){
			$.getJSON("assets/world/worlds.json", function(json) {
				var obj = json;
				console.log(obj);
				var worlds = new Array();
				Object.keys(obj).forEach(function(worldID,index){
					var world = obj[worldID];
					if(worldID > 6)
						$(".nav-pills").prepend('<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#world' + worldID + '">Current Event</a></li>');
					else
						$(".nav-pills").append('<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#world' + worldID + '">World ' + worldID + '</a></li>');
					
					$(".tab-content").append('<div class="tab-pane" id="world' + worldID + '" role="tabpanel"><div class="card world-card"><div class="card-header">' + (worldID > 6 ? locNumber(worldID,0) : world.en_us) + '</div><div class="card-block"><div class="world"></div></div></div></div></div>');
					var maps = new Array();
					Object.keys(world.maps).forEach(function(mapID){
						var worldBody = $(".tab-pane#world" + worldID).find(".world");
						var loc = locNumber(worldID,mapID);
						var fileName = "assets/world/banner/" + (worldID > 6 ? loc.split(" ").join("_") : loc) + "_Banner.png";
						//fileName = "assets/world/banner/Placeholder_Banner.png";
						worldBody.append('<a href="drop?world=' + worldID + '&map=' + mapID + '" class="' + (mapID > 4 || worldID > 6 ? "eo-" : "") + 'map"><img src="' + fileName + '"></a>');
					});

					//check if all image exists, and replace broken with placeholder
					if(index == Object.keys(obj).length - 1){
						$(".eo-map > img").each(function(){
							$(this).on("error",function(){
								$(this).attr("src","assets/world/banner/Placeholder_Banner.png");
							});
						});
					}
				});
				$(".nav-pills > .nav-item > .nav-link").first().tab('show');
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
					<ul class="nav nav-pills" role="tablist">
					</ul>
					<div class="tab-content" style="width:100%">
					</div>
				</div>
			</div>
		</div>
		
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>
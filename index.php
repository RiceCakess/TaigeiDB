<html>
	<head>
		<?php require_once ('includes/head.php');?>
		<link rel="stylesheet" href="css/search-bar.css" type="text/css">
		<script src="js/search-bar.js"></script>
		<script src="js/common.js"></script>
		<script>
			$(document).ready(function(){
				searchBar();
			});
		</script>
	</head>
	<body>
		<?php include_once ('includes/navbar.php') ?>
		<div class="overlay"></div>
		<div class="content">
			<div class="search-section" onload="">
				<div class="overlay"></div>
				<div class="main-search">
					<input class="form-control form-control-lg" type="text" placeholder="Search...">
					<ul class="list-group search-dropdown" id="livesearch">
					</ul>
				</div>
				
			</div>
		</div>
	</body>
</html>

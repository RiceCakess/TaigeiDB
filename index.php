<html>
	<head>
		<?php require_once ('includes/head.php');?>
	</head>
	<body>
		<?php include_once ('includes/navbar.php'); ?>
		<div class="content" style="background-color: white">
			<div class="search-section" onload="">
				<div class="overlay"></div>
				<div class="search-bar main-search">
					<input class="form-control form-control-lg" type="text" placeholder="Search...">
					<ul class="list-group search-dropdown" id="livesearch">
					</ul>
				</div>
			</div>
			<div class="container card-section">
				<div class="row">
					<div class="col-lg-6">
						<div class="card">
							<div class="card-header">Recent</div>
							<div class="card-body">
							
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="card">
							<div class="card-header">Updates</div>
							<div class="card-body" style="padding: 0px">
								<a class="twitter-timeline" data-height="400" href="https://twitter.com/KanColle_STAFF?ref_src=twsrc%5Etfw">Tweets by KanColle_STAFF</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php include_once ('includes/footer.php'); ?>
	</body>
</html>

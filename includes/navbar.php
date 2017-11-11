<nav class="navbar navbar-expand-lg navbar-dark">
	<script>
		$(document).ready(function() {
			var path = window.location.pathname.split("/");
			var page = path[path.length-1];
			if(page === "" || page === "index.php"){
				page = "./";
			}
			else{
				$(".sub-search").show();
			}
			$('.navbar-nav > li > a[href="'+ page +'"]').parent().addClass('active');
			searchBar();
		});
	</script>
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler">
			<span class="navbar-toggler-icon"></span>
		</button>
		<a class="navbar-brand" href="./">
			<!-- <img src="assets/whale.svg" width="100px"> !-->
			Taigei
		</a>
		
		<div class="collapse navbar-collapse" id="navbarToggler">
			
			<ul class="navbar-nav">
				<li class="nav-item ">
					<a class="nav-link" href="./">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="construction.php">Construction</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="development.php">Development</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="drops.php">Drops</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="#">Discord Bot</a>
				</li>
			</ul>
			<ul class="navbar-nav ml-auto" >
				<div class="search-bar sub-search" style="display: none;">
					<input class="form-control" type="text" placeholder="Search...">
					<ul class="list-group search-dropdown" id="livesearch">
					</ul>
				</div>
			</ul>
		</div>
	</div>
</nav>
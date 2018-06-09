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
			if(page === "droplist" || page === "worlds")
				$('.navbar-nav > li > a#dropdown').parent().addClass('active');
			
			$('.navbar-nav > li > a[href="'+ page +'"]').parent().addClass('active');
			searchBar();
		});
	</script>
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler">
			<span class="navbar-toggler-icon"></span>
		</button>
		<a class="navbar-brand" href="./">
			<img src="assets/icon/icon_48.png" width="38px">
			Taigei
		</a>
		
		<div class="collapse navbar-collapse" id="navbarToggler">
			
			<ul class="navbar-nav">
				<li class="nav-item ">
					<a class="nav-link" href="./">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="construction">Construction</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="development">Development</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Drop
					</a>
					<div class="dropdown-menu" aria-labelledby="dropdown">
					  <a class="dropdown-item" href="droplist">Drop List</a>
					  <a class="dropdown-item" href="worlds">Worlds</a>
					</div>
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
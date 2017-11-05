<nav class="navbar navbar-expand-lg navbar-dark">
	<script>
		$(document).ready(function() {
			var path = window.location.pathname.split("/");
			$('.navbar-nav > li > a[href="'+ path[path.length-1]+'"]').parent().addClass('active');
		});
	</script>
	<div class="container">
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler">
			<span class="navbar-toggler-icon"></span>
		</button>
		<a class="navbar-brand" href="../kancolle">
			<!-- <img src="assets/whale.svg" width="100px"> !-->
			Taigei
		</a>
		<div class="collapse navbar-collapse" id="navbarToggler" >
			<ul class="navbar-nav ml-auto">
				<li class="nav-item ">
					<a class="nav-link" href="index.php">Home</a>
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
			</ul>
		</div>
	</div>
</nav>
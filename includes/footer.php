<footer class="footer">
	<div class="container">
		<ul>
			<li>API</li>
			<li>Contact</li>
			<li>Privacy Policy</li>
			<li>Feedback</li>
		</ul>
		<span>Made with love by <a href="">Ricecakes</a></span>
		<div class="text-muted">Original data kindly provided by OpenDB (Wolfkangkurz)</div>
		<?php 
			$month = date('n'); 
			$src = "";
			if($month == 12 && $month <= 2){
				$src = "assets/footer-taigei2.png";
			}
			else if($month >= 3 && $month <= 5){
				$src = "assets/footer-taigei3.png";
			}
			else if($month >= 6 && $month <= 8){
				$src = "assets/footer-taigei.png";
			}
			else if($month >= 9 && $month <= 11){
				$src = "assets/footer-taigei4.png";
			}
		?>
		<img class="footer-taigei" src="<?php echo $src;?>"/>
		
	</div>
	<div class="footer-dot"/>
</footer>
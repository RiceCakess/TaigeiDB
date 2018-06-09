<footer class="footer">
	<div class="container">
		<ul class="footer-list">
			<a href="#"><li>API</li></a>
			<a href="mailto:contact@taigei.moe"><li>Contact</li></a>
			<a href="mailto:feedback@taigei.moe"><li>Feedback</li></a>
		</ul>
		<span>Made with love by <a href="">Ricecakes</a></span>
		<div>Original data kindly provided by OpenDB</div>
		<?php 
			$month = date('n'); 
			$src = "";
			if($month == 12 || $month <= 2){
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
<script src="js/common.js"></script>
<script src="js/search-bar.js"></script>
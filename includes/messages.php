<?php
class Message {

	public static function msg($titel,$message,$time,$type) {
		/*
		 * error type
		 * success
		 * error
		 * info
		 */
	echo "<script type=\"text/javascript\">
            var delay = (function() {
				
			var timer = 0;
			return function(callback, ms) {
				clearTimeout(timer);
				timer = setTimeout(callback, ms);
				};
			})();
            
           
		delay(function() {
			new PNotify({
				title : '".$titel."',
				text : '".$message."',
				type : '".$type."'
				});
		}, ".$time.");
		</script>";
		}
	
	public static function redirect($page, $time="3000"){
		echo "<script type=\"text/javascript\">
				setTimeout(function() {
  				window.location.href = '".$page."';
				}, ".$time.");
		</script>";
	}

}
?>
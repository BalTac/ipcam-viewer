<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>IPCams Body</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<link href="js/zoomify/dist/zoomify.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/zoomify/dist/zoomify.js"></script>
<style>
	#box {
		display: inline-block;
		border:1px dotted transparent;
		margin: 1em;
	}
	img.ipstr {
		height: 240px;
		padding:3px;
		margin:5px;
		border-radius:5px;
		border:1px solid #555;
		box-shadow: 10px 10px 5px #000;
	}
	img.ipstr:hover {
		border-radius:5px;
		-webkit-animation: mymove 2s infinite; /* Chrome, Safari, Opera */
		animation: mymove 2s infinite;
	}
	/* Chrome, Safari, Opera */
	@-webkit-keyframes mymove {
		50% {background-color:#888;
			border:1px groove #aaa;
			box-shadow: 0px 0px 20px #fff;}
	}
	/* Standard syntax */
	@keyframes mymove {
		50% {background-color:#888;
			border:1px groove #aaa;
			box-shadow: 0px 0px 20px #fff;}
	}
	/* Chrome, Safari, Opera */
	@-webkit-keyframes mymove2 {
		50% {color: rgba(26, 121, 119, 0.2)}
	}
	/* Standard syntax */
	@keyframes mymove2 {
		50% {color: rgba(26, 121, 119, 0.2)}
	}
</style>
</head>
<?php include 'myfunctions.php'; ?>
<?php 
	if ((substr($_SERVER['REMOTE_ADDR'],0,8) == "192.168.") || ($_SERVER['REMOTE_ADDR'] == "127.0.0.1")) {
		// client is local
		$pip = $_SERVER['SERVER_ADDR'];
		if ($pip == "127.0.0.1") {
			$pip = "localhost";
		}
	} else {
		// client is not local
		$pip = extip();
	}
?>
    <body>
			<div class="box">
				<img class="ipstr" src="http://<?php echo $pip; ?>:8082"></img>
				<img class="ipstr" src="http://<?php echo $pip; ?>:8083"></img>
				<img class="ipstr" src="http://<?php echo $pip; ?>:8085"></img>
			</div>
	<script>
		$('img').zoomify({scale:1});
	</script>
    </body>
</html>
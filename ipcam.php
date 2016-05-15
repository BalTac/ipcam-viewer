<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>IPCams Casa</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="icon" type="image/png" href="images/ipcam.png" />
	<script type="text/javascript">
		var hasFocus;
		window.onblur = function() {
			hasFocus = false;
		}
		window.onfocus = function(){
			hasFocus = true;
		}
		setInterval(reload, 300*1000);
		function reload(){
			if(hasFocus){
				location.reload(true);
			}
		}
	</script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
	<link rel="stylesheet" href="js/fancyBox/source/jquery.fancybox.css" type="text/css" media="screen" />
	<script type="text/javascript" src="js/fancyBox/source/jquery.fancybox.pack.js"></script>
	<?php include 'myfunctions.php'; ?>
	<style>
	body {
		background-color:black;
		color:white;
	}
	#index {
		margin: 0 auto 0 auto;
		width:80%;
        	min-width: 940px;
		color:white;
		border-radius: 5px;
		text-align:center;
		padding: 10px;
		background: url(images/pattern1.jpg);
		box-shadow: inset 0px 0px 10px #555;
	}
	#index #header {
		position: relative;
		height: 99px;
		margin: 5px;
		border: 3px groove #666;
		border-radius: 10px;
		background:	url(images/seamless-leather.jpg);
		box-shadow: 0px 0px 20px #888;
	}
	#plate {
		margin: 5px;
		position: relative; 
		height: 88px;
		width: 275px;
		background: url(images/silver_plate.png) -62px -67px;
	}
	@font-face {
		font-family: 'Impacted';
		src: url('fonts/impacted/Impacted.eot?#iefix') format('embedded-opentype'),  url('fonts/impacted/Impacted.woff') format('woff'), url('fonts/impacted/Impacted.ttf')  format('truetype'), url('fonts/impacted/Impacted.svg#Impacted') format('svg');
		font-weight: normal;
		font-style: normal;
	}
	@font-face {
		font-family: 'BebasNeuewebfont';
		src: url('./fonts/BebasNeuewebfont/BebasNeuewebfont.eot');
		src: local('BebasNeuewebfont'), url('./fonts/BebasNeuewebfont/BebasNeuewebfont.woff') format('woff'), url('./fonts/BebasNeuewebfont/BebasNeuewebfont.ttf') format('truetype');
	}
	h2 {
		font-family: 'Impacted' !important;
		font-weight: bold;
		font-size: 2em;
		line-height: 1em;
	}
	#title {
		position: relative; 
		top: -97px; 
		left: 0px; 
		width: 100%; 
		color: rgba(90,95,100, 0.7);
        text-shadow: 1px 2px 4px #bbb, 0 0 0 #000, 1px 2px 6px #bbb;
		-webkit-animation: mymove2 5s infinite ease-out; /* Chrome, Safari, Opera */
		animation: mymove2 5s infinite ease-out;
	}
	#subtitle {
		font-family: 'BebasNeuewebfont', Arial, Helvetica, sans-serif;
		font-weight: bold;
		font-size: 1em;
		line-height: 1em;
		position: relative; 
		top: -140px; 
		left: 0px; 
		width: 100%; 
		color: rgba(90,95,100, 0.6);
        text-shadow: 1px 1px 2px #bbb, 0 0 0 #000, 1px 1px 2px #bbb;
		-webkit-animation: mymove2 2s infinite ease-out; /* Chrome, Safari, Opera */
		animation: mymove2 2s infinite ease-out;
	}
	#index .box {
		position:relative;
		display: inline-block;
		border:1px dotted transparent;
		margin: 1em;
	}
	#index .statusbar {
		height: 85px;
		padding:3px;
		margin:5px;
		border-radius:5px;
		border:1px solid #555;
		box-shadow: 10px 10px 5px #000;
	}
	#index .statusbar .blackbox {
		display:inline-block;
		overflow: hidden;
		background:black; 
		height:100%; 
		width:100%; 
		position:relative;
	}
	table .panelinfo, tr {
		border:1px solid #333;
		border-collapse: collapse;
		margin: 0px;
		padding: 0px;
		text-align:left;
		font-family: 'BebasNeuewebfont', Arial, Helvetica, sans-serif; 
		font-size:13px; 
		color:#A7C6FF; 
		text-shadow: 0 0 5px #00c6ff;
		width:100%;
	}
	td {
		width:100px;
		padding: 0px 5px 0px;
	}
	img.ipstr {
		position:relative;
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
	.ipstr-div {
		position:relative;
		display:inline-block;
	}
	.ipstr-text {
		position:absolute;
		top: 9px;
		left: 9px;
		padding: 0px 3px 0px 3px;
		border-radius: 3px;
		font-family: 'BebasNeuewebfont', Arial, Helvetica, sans-serif; 
		font-size:13px; 
		color:#FFFFFF; 
		text-shadow: 0 0 5px #00c6ff;
		background: rgba(0, 0, 0, 0.5);
	}
	.pulse {
		display:inline-block; 
		background: #fff; 
		height:5px; 
		width:0px; 
		border:0px solid #fff;
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
<?php 
	if ((substr($_SERVER['REMOTE_ADDR'],0,8) == "192.168.") || ($_SERVER['REMOTE_ADDR'] == "127.0.0.1")) {
		// client is local
		$pip = $_SERVER['SERVER_ADDR'];
		if ($pip == "127.0.0.1") {
			$pip = "localhost";
			$color = 'lightgreen';
		} else {$color = 'yellow';}
	} else {
		// client is not local
		$pip = extip();
		$color = 'red';
	}
?>
    <body>
        <div id="index">
			<div id="header">
				<img id="plate" src="images/img_trans.gif">
					<span id="title"><h2>IPCams Casa</h2></span>
					<br><span id="subtitle" style="color: <?php echo $color;?>;"><?php echo $pip;?></span>
				</img>
			</div>
		
			<div class="box">
				<div class="ipstr-div"><a class="fancybox" data-type="iframe" 	href="ip_big.php?cam=http://<?php echo $pip; ?>:8082"><img id="cam1" class="ipstr" src="http://<?php echo $pip; ?>:8082"></img></a><div id="fpscam1" class="ipstr-text"></div></div>
				<div class="ipstr-div"><a class="fancybox" data-type="iframe" 	href="ip_big.php?cam=http://<?php echo $pip; ?>:8083"><img id="cam2" class="ipstr" src="http://<?php echo $pip; ?>:8083"></img></a><div id="fpscam2" class="ipstr-text"></div></div>
				<div class="ipstr-div"><a class="fancybox" data-type="iframe" 	href="ip_big.php?cam=http://<?php echo $pip; ?>:8085"><img id="cam3" class="ipstr" src="http://<?php echo $pip; ?>:8085"></img></a><div id="fpscam3" class="ipstr-text"></div></div>
			</div>
			<div class="statusbar">
				<div class="blackbox">
					<iframe id="drivespace" src="./dspace2.html" 	width="300" 	style="float:left; height:85px; margin:0px; border:0px; padding:0px; display:block;"></iframe>
					<div 	id="panel"												style="border:1px solid #333; display:inline-block; width:300px; height:65px; margin:9px;">
						<table class="panelinfo">
							<tr><td id="pulsecam1"></td><td id="pulsecam4"></td><td id="pulsecam7"></td></tr>
							<tr><td id="pulsecam2"></td><td id="pulsecam5"></td><td id="pulsecam8"></td></tr>
							<tr><td id="pulsecam3"></td><td id="pulsecam6"></td><td id="pulsecam9"></td></tr>
						</table>
							
					</div>
					<iframe id="clock" 		src="./clock.html" 		width="300"		style="float:right; height:85px; margin:0px; border:0px; padding:0px; display:block; display:inline-block;"></iframe>
				</div>
			</div>
		</div>
	<audio id="flash" preload="auto">
		<source src="audio/camera_flash.mp3"></source>
		This browser does not support the HTML5 audio tag.
	</audio>
	<audio id="click" preload="auto">
		<source src="audio/click.mp3"></source>
		This browser does not support the HTML5 audio tag.
	</audio>	
	<script type="text/javascript">
	var flash = $("#flash")[0];
	var click = $("#click")[0];
	$("img.ipstr")
	.mouseenter(function() {
		flash.play();
		checkfps(this.id, true);
	})
	.mousedown(function() {
		click.play();
	});
		function checkfps(cam) { 
			var last, diff, sum=0;
			$('#'+cam).on('load', function( event ) {
				if ( last ) {
					diff = event.timeStamp - last;
					if ( last>999999999 ) {
						fps=1000000/diff;
						sum+=(diff/1000000);

					} else {
						fps=1000/diff;
						sum+=(diff/1000);
					}
					fps=(Math.round(fps*100)/100).toFixed(1);
					$('<div class="pulse" style="width:'+fps+'px;"></div>').appendTo('#pulse'+cam).delay(1000).fadeIn(function() {$(this).remove();});
					if ( sum>=1 ) {
						$('#fps'+cam).html('fps: '+fps);
						sum=0;
					}
				}
				last = event.timeStamp;
			});
		}
	var ids = $('.ipstr').map(function(index) {
		// this callback function will be called once for each matching element
		return this.id; 
	});
	
	jQuery(document).ready(function($){
		$.each(ids, function( index, value ) {
			checkfps(value);
		});
		$(".fancybox").on("click", function(){
			$.fancybox({
				href: this.href,
				type: $(this).data("type"),
				padding: '0'
			}); // fancybox
			return false   
		}); // on
	}); // ready
	</script>

    </body>
</html>

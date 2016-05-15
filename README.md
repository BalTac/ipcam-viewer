# ipcam-viewer
Home iPCamera remote survey frontend in PHP and JQuery for home servers and DVR.

Actually this project was not intended for public distribution, but since I have to keep updated this code 
in more than one server, I decided to use github to synchronize changes. 

There are many project like this and really better than this, out there. So if you are looking for a well coded and full working
frontend for your home server, please, simply ignore this work. But if you want use my code or contribute to this project 
in any way, you're wellcome.

I had never coded JQuery before, and I'm also beginner regarding PHP so any suggestions or constructive
critic can be useful.

INSTALLATION NOTES
------------------
You must have an home server with PHP5 support and a browser with JAVA support. Personally I use an old Acer Aspire ONE with 
a custom Ubuntu distribution, called BackBox (https://backbox.org). The project is mainly frankenstein-coded (stealing, reworking and sawing parts of code all over the web) and debugged using FireFox but I've tried in Google Chrome and Safari without issues.

Put the files in your root WWW folder, or in any subfolder and use your browser to show it. 
Simply call <pre>http://your_server_ip_or_name/your_folder/ipcam.php</pre>

CONFIGURATION
-------------
Soon I'll build a config.php file for this, but for now...

In ipcam.php:
   - Search for <pre><<span>div class="box"</span>>...</pre> and edit the URLs in: <pre><<span>a class="fancybox" href...</span></pre> and in: <pre><<span>img class="ipstr" src...</span></pre> according to your camera videostream sources.
   In my case I use Mr-Dave Motion for linux (https://github.com/Mr-Dave/motion) to collect all the video streams from my old ip cameras. Using this program you can manage motion detection, recordings, videostreams in one place, and finally make it available to the localhost and then forwarding them to the web. But if you want, without using any intermediary software at all, you can configure every single ip camera to be exposed directly through the frontend. To find the appropriate url to expose your ip camera video stream, please refer to the manufacturer manual, or google for it. There are large Camera Connection Databases over the web...
  
   - Change title text and <pre><<span>span id="title"</span>></pre> according to your needs.

In dspace2.php
   - Change the following code using the path of the HDD where you put your cams recodings to monitor the remaining storage.
   (or change it to monitor any other drive you want, LOL)
   <pre>
   	/* get disk space free (in bytes) */
	$df = disk_free_space("/mnt/USB3Store");
	/* and get disk space total (in bytes)  */
	$dt = disk_total_space("/mnt/USB3Store");
   </pre>

That's all for now. ^^ Enjoy. :)

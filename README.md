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
You must have an home server with PHP5 and a browser with JAVA support. Personally I use an old Acer Aspire ONE with 
a custom Ubuntu distribution, called BackBox (https://backbox.org). The project is mainly hand-coded and debugged
using FireFox but I've tried in Google Chrome and Safari without problems.

Put the files in your root WWW folder, or in any subfolder and use your browser to show it. 
Simply call http://your_server_ip_or_name/your_folder/ipcam.php

CONFIGURATION
-------------
Soon I'll commit a config.php file for this, but for now...

In ipcam.php:
   - Search for <pre><<span>div class="box"</span>>...</pre> and modify the <pre><<span>a class="fancybox" href...</span></pre> and the <pre><<span>img class="ipstr" src...</span></pre> location, according to your needs.
  
   - Change title text and <pre><<span>span id="title"</span>></pre> according to your needs.

In dspace2.php
   - Change the following code using the path where you put your cams recodings to monitor your recorder space.
   (or change it to monitor any other drive you want, LOL)
   <pre>
   	/* get disk space free (in bytes) */<br>
	$df = disk_free_space("/mnt/USB3Store");<br>
	/* and get disk space total (in bytes)  */<br>
	$dt = disk_total_space("/mnt/USB3Store");<br>
   </pre>
That's all for now. ^^ Enjoy. :)

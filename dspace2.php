<?php
	header("Content-Type: application/json");
    function formatSize( $bytes )
    {
            $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
            for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
                    return( round( $bytes, 2 ) . " " . $types[$i] );
    }
	/* get disk space free (in bytes) */
	$df = disk_free_space("/mnt/USB3Store");
	/* and get disk space total (in bytes)  */
	$dt = disk_total_space("/mnt/USB3Store");
	/* now we calculate the disk space used (in bytes) */
	$du = $dt - $df;
	/* percentage of disk used - this will be used to also set the width % of the progress bar */
	$dp = sprintf('%.2f',($du / $dt) * 100);

	/* and we formate the size from bytes to MB, GB, etc. */
	$df = formatSize($df);
	$du = formatSize($du);
	$dt = formatSize($dt);
	echo json_encode(array(array("dp" => $dp, "df" => $df, "du" => $du, "dt" => $dt)));
?>   
 

<!DOCTYPE html>
<html lang="en">
<?php require_once('getjsonweb.php'); ?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>my BitTorrent stats</title>
<script src="out/utils.js"></script>
<script>var KW_TSTATS_INIT_O    = false;</script>
<script src="out/js1.js"></script>

<style>
    table.statTable {
	font-family: monospace;
    }
    
    td, th { padding-right: 1.0ex }
    
    body { font-family: sans-serif }
    .explanationTop { 
	margin: 0;
	display: inline-block;
    }
    </style>

</head>
<body>

    <div>
	<p class="explanation explanationTop">Explanation below the data.</p>
	<button onclick="window.history.go(0);">redo</button>
    </div>
    
    <div>last tracker check (min ago): <span id='lmago' /></div>
    <div>
	<div id='stattabc'></div>
	<div>
	    <div><span id='fname' /></div>
	    <div><span id='totszh' /></div>	    
	</div>
    </div>
    
    <div class="explanation explanation1">
    <p>This documents my progress in reaching a 1:1 and beyond BitTorrent ratio for the given file ("Name") and "Total size" of that file.    </p>
    <p>The fields are the ratio, the "one digit floor()" ratio, MB uploaded, number of seeders, leeches, and that ratio for each of IPv4 and v6, 
	and seed time.  The seed time is the time that my client has been running and offering the file, regardless of whether any data goes "up."    </p>
    </div>
   
    <p>This source code is on GitHub: 
	<a href ='https://github.com/kwynncom/transmission-stats-parser-and-web-display'>https://github.com/kwynncom/transmission-stats-parser-and-web-display</a>
    </p>
    
</body>
<script>KW_TSTATS_INIT_O = <?php echo getjsonweb(); ?>;</script>
</html>

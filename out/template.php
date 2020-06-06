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
    
    .explanation { font-family: sans-serif }
    .explanationTop { 
	margin: 0
    }
    </style>

</head>
<body>

    <p class="explanation explanationTop">Explanation below the data.    </p>
    
    <div id='stattabc'></div>
    
    <div class="explanation explanation1">
    <p>This documents my progress in reaching a 1:1 and beyond BitTorrent ratio for Ubuntu 20.04 desktop (64 bit), which is 2.72 GB.    </p>
    <p>The fields are ratio, MB uploaded (out of 2.72 GB), number of seeders, leeches, and that ratio for each of IPv4 and v6, 
	and seed time.  The seed time is the time that my client has been running and offering the file, regardless of whether any data goes "up."    </p>
    </div>
   

    
</body>
<script>KW_TSTATS_INIT_O = <?php echo getjsonweb(); ?>;</script>
</html>

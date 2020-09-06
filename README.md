# transmission-stats-parser-and-web-display
Gets Transmission BitTorrent stats and posts them to the web.

https://kwynn.com/t/20/06/tstats/  - running at

The /opt/kwynn code is at  https://github.com/kwynncom/kwynn-php-general-utils

RUNNING COMMENTARY - from latest to earliest

2020

09 (Sept)

5th

7:41pm

taking clock code from https://github.com/kwynncom/javascript-synchronized-clock/blob/master/js/kwClockGeneral.js ; 2020/09/05 7:07pm
see credit to Aaron Farrar in the other project's readme


08 (August)

31st / 31

7:18pm - I think it's working.  I had forgotten the interaction between the "filter20" filter and the "cutme" filter.  

5:49pm - Well, wrong in that it will take a very large, fast change to see new data.
5:47pm - That means that clido1 output is wrong, too.

5:42pm EDT / GMT -4 / Atlanta / New York

My new filter algorithm is wrong.  I am comparing the readings 20 seconds apart, so they will never be over 5 minutes.  Also, those are not the upload 
numbers I should be comparing.  The correct algorithm is to track the data last sent to the server / web.  I should be comparing that with the latest reading.  
Perhaps I'll fix this later today / tonight.


2020/08/30 9:40pm EDT / GMT -4

Rather than the daemon version running every 5 minutes, now it runs every 20 seconds and only POSTs to the web if either 5 minutes have passed or 
the relevant values get bigger.

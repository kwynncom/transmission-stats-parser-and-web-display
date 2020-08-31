# transmission-stats-parser-and-web-display
Gets Transmission BitTorrent stats and posts them to the web.

https://kwynn.com/t/20/06/tstats/  - running at

The /opt/kwynn code is at  https://github.com/kwynncom/kwynn-php-general-utils

RUNNING COMMENTARY

2020/08/30 9:40pm EDT / GMT -4

Rather than the daemon version running every 5 minutes, now it runs every 20 seconds and only POSTs to the web if either 5 minutes have passed or 
the relevant values get bigger.

#!/bin/bash

sed '/exiting/d' /var/www/pifm.log.txt | aha -b > /var/www/pifm.log.htm
sed -i '/MUSIC_ROOT\|delphigl/d' /var/www/pifm.log.htm
sed -i 's/\<sudo -u www-data\>//g' /var/www/pifm.log.htm
sed -i "8s/.*/<body onload='pageScroll()' style='color:white; background-color:black'>/" /var/www/pifm.log.htm
sed -i "5i <script>function pageScroll() {window.scrollBy(0,5000);scrolldelay = setTimeout('pageScroll()',10);}</script>" /var/www/pifm.log.htm
sed -i "11i <p style='font-weight:normal;color:#E6E6E6;background-color:#000000;letter-spacing:0.3pt;word-spacing:1.2pt;font-size:9.5px;text-align:left;font-family:tahoma, verdana, arial, sans-serif;line-height:1.1;margin:0px;padding:0px;'>" /var/www/pifm.log.htm
tail -n 4 pifm.log.htm | head -n 1 | sed 's/^ sox //' > now.txt

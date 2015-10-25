#!/bin/bash

sed '/exiting/d' /var/www/pifm.log.txt | aha -b > /var/www/pifm.log.htm
sed -i '/MUSIC_ROOT\|delphigl/d' /var/www/pifm.log.htm
sed -i 's/\<sudo -u www-data\>//g' /var/www/pifm.log.htm
sed -i "8s/.*/<body onload='pageScroll()' style='color:white; background-color:black'>/" /var/www/pifm.log.htm
sed -i "5i <script>function pageScroll() {window.scrollBy(0,50);scrolldelay = setTimeout('pageScroll()',20);}</script>" /var/www/pifm.log.htm


<?php
exec("sed -i '/exiting/d' /var/www/pifm.log.txt");
exec('cat /var/www/pifm.log.txt | aha -t "RaspberryPi 100MHz FM Radio" -b > /var/www/pifm.log.htm');
header("Refresh: 0;url=pifm.log.htm"); 
exit();
?>

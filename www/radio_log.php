<?php
exec("sed '/exiting/d' /var/www/pifm.log.txt | aha -b > /var/www/pifm.log.htm");
exec("sed -i '/MUSIC_ROOT\|delphigl/d' /var/www/pifm.log.htm");
header("Refresh: 0;url=pifm.log.htm"); 
exit();
?>

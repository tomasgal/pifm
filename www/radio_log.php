<?php
exec("sh /var/www/script.sh");
header("Refresh: 0;url=pifm.log.htm"); 
exit();
?>

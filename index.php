<pre><strong>System Information:</strong><br><?php system("inxi -! 31 -C -D -f -I -S -c0"); ?>
CPU Governor is set to: <?php system("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor"); ?><br>
<strong>Now Processing:</strong><br><?php system("ps -p `pgrep sox` -o command --noheader"); ?><br>
<strong>List has been generated at:</strong> <?php system("stat  --format=%z /var/tmp/list.txt"); ?>
<strong>Files in Repository:</strong> <?php system("ls /radio_nas/ | wc -l"); ?><br>
<strong>Memory Usage (MB):</strong><br><?php system("free -mht"); ?><br>
<strong>Disk Usage:</strong><br /><?php system("df -Th /dev/mmcblk0p2"); ?><br>
<?php system("inxi -xxxW 48.1462538,17.1130809 -c0"); ?><br></pre>
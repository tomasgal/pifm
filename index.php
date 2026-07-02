<?php
/*
 * Historical local status page for the PiFM Raspberry Pi transmitter setup.
 *
 * This page was intended for a trusted local network. It executes shell
 * commands and prints their output, so it should not be exposed to an
 * untrusted network without a complete rewrite and proper hardening.
 */
?>
<pre><strong>System Information:</strong><br><?php system("inxi -! 31 -C -D -f -I -S -c0"); ?>
CPU Governor is set to: <?php system("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_governor"); ?><br>
<strong>Now Processing:</strong><br><?php system("ps -p `pgrep sox` -o command --noheader"); ?><br>
<strong>List has been generated at:</strong> <?php system("stat --format=%z /var/tmp/list.txt"); ?>
<strong>Files in Repository:</strong> <?php system("ls /radio_nas/ | wc -l"); ?><br>
<strong>Memory Usage (MB):</strong><br><?php system("free -mht"); ?><br>
<strong>Disk Usage:</strong><br /><?php system("df -Th /dev/mmcblk0p2"); ?><br>
<?php
// Optional historical weather output. The original deployment used precise
// local coordinates here. They are intentionally not included in the public
// repository. To re-enable locally, replace LATITUDE,LONGITUDE with your own
// coordinates and uncomment the following line.
// system("inxi -xxxW LATITUDE,LONGITUDE -c0");
?>
</pre>

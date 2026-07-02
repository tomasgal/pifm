#!/bin/bash
#
# Historical Wi-Fi watchdog for the PiFM Raspberry Pi setup.
#
# The original Raspberry Pi / Raspbian deployment sometimes lost Wi-Fi
# connectivity. This loop checks whether wlan0 has an IPv4 address in the old
# ifconfig output and forces the interface back up when it does not.
#
# This reflects an old net-tools / ifupdown style networking environment. A
# current system would normally use systemd-networkd, NetworkManager, dhcpcd, or
# another maintained network manager instead of this loop.

while true ; do
  if ifconfig wlan0 | grep -q "inet addr:" ; then
    sleep 60
  else
    echo "Network connection down! Attempting reconnection."
    ifup --force wlan0
    sleep 10
  fi
done

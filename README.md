# RPiFM

Raspberry Pi FM transmitter experiment with a small web status interface and playlist automation.

This repository documents a historical Raspberry Pi project built around PiFM: a way to generate a low-power FM signal from a Raspberry Pi and play audio files over ordinary FM radio receivers. The project added local automation around that idea: a generated playlist, a boot-time playback loop, a simple web page showing what the system was doing, and a Wi-Fi watchdog for the unstable Raspberry Pi networking environment of the period.

## Legal and safety notice

Radio transmission is regulated. This repository is a historical experiment and documentation snapshot, not a recommendation to operate an FM transmitter. Do not transmit outside the legal limits of your jurisdiction. Use appropriate filtering, power limits, shielding, and local regulatory guidance if experimenting with RF hardware.

## Project status

This is a historical build and operations snapshot. It is not a maintained audio streaming project and should not be treated as a current deployment guide.

The original setup depended on an old Raspberry Pi software environment, PiFM behavior, `rc.local`, `ifconfig`/`ifup`, `avconv`, `sox`, cron jobs, and local filesystem paths. Modern Raspberry Pi OS releases, audio tools, init systems, networking stacks, and legal/regulatory expectations have changed. Any current use should be redesigned from scratch and reviewed for legality and safety.

## Overview

The intended architecture was:

```text
MP3 repository
    -> generated random playlist
    -> boot-time playback loop
    -> sox / avconv audio conversion
    -> PiFM
    -> low-power FM signal
    -> nearby FM receiver
```

The repository also contains a simple PHP status page. In this version it is primarily a viewer: it shows system information, CPU governor state, currently running audio processing command, playlist timestamp, repository size, memory and disk usage, and optional weather output.

## Main files

| File | Purpose |
| --- | --- |
| `index.php` | Historical local web status page. It shows system and playback information; it is not a secured public web UI. |
| `network-monitor.sh` | Simple Wi-Fi watchdog for old Raspbian-style networking. |
| `notes.txt` | Historical crontab notes, including hourly playlist generation. |
| `playfm` | Adapted playback wrapper that scans the music directory, builds a playlist, converts audio, and pipes it into PiFM. |
| `rc.local` | Historical boot-time startup script. It generates a shuffled playlist and starts an endless playback loop. |
| `www/20150627.JPG` | Project photograph. |

## Playlist generation

The historical crontab reconstructed in `notes.txt` generated a random list of MP3 files every hour:

```cron
0 * * * * ls /radio_nas/*.mp3 | sort -R | head -1000 > /var/tmp/list.txt
```

The same notes also contain a daily cache-drop command:

```cron
5 0 * * * sync && echo 3 > /proc/sys/vm/drop_caches
```

This reflects the practical constraints of the original Raspberry Pi deployment. It should not be copied to a modern system without understanding the consequences.

## Boot-time behavior

The historical `rc.local` script performed these steps:

1. set SD-card read-ahead,
2. remove old PiFM temporary files,
3. generate `/var/tmp/list.txt` from `/radio_nas/*.mp3`,
4. repeatedly select one random item from the generated list,
5. call `/pifmplay` with the selected file.

The script also documents that `/var/tmp` was mounted as a small tmpfs in `/etc/fstab`.

## Playback wrapper

`playfm` contains a more self-contained playback approach. It scans the music directory, filters supported audio files, optionally shuffles them, converts each track to mono WAV through `sox` and `avconv`, and pipes the result into the PiFM binary.

The repository therefore contains more than one historical playback approach. `rc.local` shows a boot-time loop using `/pifmplay` and `/var/tmp/list.txt`, while `playfm` is a fuller wrapper that builds and plays its own playlist.

## Network watchdog

`network-monitor.sh` is a simple historical workaround for Wi-Fi instability. It checks whether `wlan0` has an IPv4 address in the old `ifconfig` output and forces `ifup wlan0` when it does not. Current Linux networking would normally be handled through systemd-networkd, NetworkManager, dhcpcd, or other maintained network tooling.

## Web status interface

`index.php` is a local diagnostic page. It executes shell commands through PHP and prints their output. This was useful for a trusted local network experiment, but it is not appropriate for exposure to an untrusted network.

The original version also contained precise coordinates for weather output. Those have been removed from the public repository and replaced by a placeholder comment.

## Known limitations

- This is a historical Raspberry Pi / PiFM experiment, not a maintained transmitter stack.
- FM transmission is legally regulated and can interfere with other services if misused.
- The original scripts use old Raspbian conventions such as `rc.local`, `ifconfig`, and `ifup`.
- `avconv` has largely been replaced by `ffmpeg` in many modern environments.
- The web UI executes local shell commands and should be treated as local-only diagnostic tooling.
- Hard-coded paths such as `/radio_nas`, `/pifm`, `/pifmplay`, and `/var/tmp/list.txt` reflect the original deployment.
- The playback scripts assume a specific local music repository and old PiFM behavior.
- The repository is useful for historical reconstruction, not for direct modern deployment.

## Image

![RPiFM](www/20150627.JPG)

## Authorship

Project notes, web UI additions, integration work, and project photograph by Tomáš Gál, unless otherwise noted.

The PiFM-related playback script is an adapted historical script and should be read as part of this deployment snapshot rather than as a maintained upstream project.

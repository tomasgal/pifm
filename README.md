# RPiFM

Raspberry Pi FM transmitter experiment with a small web status interface and playlist automation.

This repository documents a historical Raspberry Pi project built around PiFM: a way to generate a low-power FM signal from a Raspberry Pi and play audio files over ordinary FM radio receivers. The project added local automation around that idea: a generated playlist, a boot-time playback loop, a simple web page showing what the system was doing, and a Wi-Fi watchdog for the unstable Raspberry Pi networking environment of the period.

## Legal and safety notice

Radio transmission is regulated. This repository is a historical experiment and documentation snapshot, not a recommendation to operate an FM transmitter. Do not transmit outside the legal limits of your jurisdiction. Use appropriate filtering, power limits, shielding, and local regulatory guidance if experimenting with RF hardware.

## Project status

This is a historical build and operations snapshot. It is not a maintained audio streaming project and should not be treated as a current deployment guide.

The original setup depended on an old Raspberry Pi software environment, PiFM behavior, `rc.local`, `ifconfig`/`ifup`, `sox`, cron jobs, and local filesystem paths. Modern Raspberry Pi OS releases, audio tools, init systems, networking stacks, and legal/regulatory expectations have changed. Any current use should be redesigned from scratch and reviewed for legality and safety.

## 2026 maintenance pass

In 2026 the appliance was reviewed and lightly maintained after roughly six years since the Raspbian Buster appliance build. The goal was not to modernize the stack or turn it into a maintained project. The goal was to preserve a working long-running Raspberry Pi Zero radio appliance while removing a few fragile parts.

The maintenance pass focused on small, low-risk changes:

- the web status update was moved from a cron/process-list parser into the playback loop;
- `rc.local` now writes the selected track title directly to `/var/ramdrive/index.html` before playback starts;
- `playfm` was reduced to the only path actually used by the appliance: MP3 file -> `sox` -> `pifm`;
- old playback branches for URLs, folders, pipes, YouTube, `avconv`/`ffmpeg`, wav/m4a fallbacks, and pause/resume/stop commands were removed from the maintained wrapper;
- automatic apt/man-db housekeeping was disabled on the running appliance to reduce unnecessary background activity on the SD-card based system.

The resulting snapshot is still deliberately conservative: it keeps the historical PiFM binary, Raspbian-era assumptions, `rc.local`, the small ramdrive status file, and the local mini_httpd status endpoint.

## Overview

The maintained appliance architecture is:

```text
MP3 repository on /radio_nas
    -> hourly random playlist in /var/ramdrive/list.txt
    -> boot-time rc.local playback loop
    -> selected title written to /var/ramdrive/index.html
    -> /pifmplay playback wrapper
    -> sox MP3 decoding and mono WAV conversion
    -> PiFM binary
    -> low-power FM signal
    -> nearby FM receiver
```

The web status interface is intentionally minimal. `mini_httpd` serves the RAM-backed `index.html` file from `/var/ramdrive`, so the status page shows only the current track name and does not need a PHP diagnostic page in the maintained appliance variant.

## Main files

| File | Purpose |
| --- | --- |
| `index.php` | Historical local web status page. It shows system and playback information; it is not a secured public web UI. The maintained appliance variant uses a simpler RAM-backed `index.html` status file. |
| `network-monitor.sh` | Simple Wi-Fi watchdog for old Raspbian-style networking. |
| `notes.txt` | Historical and maintained crontab notes, including hourly playlist generation and the retired status-parser cron line. |
| `playfm` | Simplified repository copy of the installed `/pifmplay` wrapper. It plays one MP3 file through `sox` and the PiFM binary. |
| `rc.local` | Boot-time startup script. It generates a shuffled playlist, writes the current track status, and starts an endless playback loop. |
| `www/20150627.JPG` | Project photograph. |

## Playlist generation

The maintained appliance keeps a randomized playlist in a small RAM-backed filesystem:

```cron
@hourly ls /radio_nas/*.mp3 | sort -R | head -1000 > /var/ramdrive/list.txt
```

`rc.local` also generates the same file during startup before entering the playback loop.

Older historical notes used `/var/tmp/list.txt`; the maintained appliance version uses `/var/ramdrive/list.txt` to make the tmpfs/ramdrive role explicit.

## Boot-time behavior

The maintained `rc.local` script performs these steps:

1. generate `/var/ramdrive/list.txt` from `/radio_nas/*.mp3`,
2. repeatedly select one random item from the generated list,
3. derive a display title by removing the directory path and `.mp3` suffix,
4. write that title to `/var/ramdrive/index.html`,
5. call `/pifmplay` with the selected file.

This means that `rc.local` now takes over the status-update function that was previously handled by cron. Cron remains suitable for periodic playlist refreshes, but it is no longer responsible for discovering the currently playing track.

## Why the status cron was retired

The earlier appliance used a cron job to update the web status file. The job repeatedly inspected the process list, looked for the running `sox` process, extracted the MP3 filename from the command line, stripped the path and suffix, and wrote the result to the web status file.

That design worked, but it was a workaround. It depended on process-list formatting, timing, the existence of exactly the expected `sox` command line, and shell text processing. It also tried to answer a question indirectly: "what is currently playing?" by observing a side effect of playback.

The maintained design answers the same question at the point where the decision is made. The playback loop already selects the next MP3 file before calling `/pifmplay`; therefore it can write the display title directly to `/var/ramdrive/index.html`. This makes `rc.local` the source of truth for both playback and status:

```text
song="$(shuf -n 1 /var/ramdrive/list.txt)"
write display title to /var/ramdrive/index.html
start /pifmplay "$song"
```

This removes the fragile process parser, reduces cron activity, and keeps the status file in RAM. If the web status is wrong, the bug is now close to the playback decision, not in a separate periodic observer.

## Playback wrapper

`playfm` is the repository copy of the simplified playback wrapper installed as `/pifmplay` on the appliance.

The maintained wrapper does one thing:

```text
MP3 file -> sox -v 0.9 -t mp3 ... -r 22050 -c 1 -> /pifm - 100.0
```

The previous generic script supported more modes, but the running appliance only used local MP3 files. The reduced wrapper is easier to understand and avoids unused dependencies such as `avconv`/`ffmpeg` for the normal path.

## Network watchdog

`network-monitor.sh` is a simple historical workaround for Wi-Fi instability. It checks whether `wlan0` has an IPv4 address in the old `ifconfig` output and forces `ifup wlan0` when it does not. Current Linux networking would normally be handled through systemd-networkd, NetworkManager, dhcpcd, or other maintained network tooling.

## Web status interface

`index.php` is a historical local diagnostic page. It executes shell commands through PHP and prints their output. This was useful for a trusted local network experiment, but it is not appropriate for exposure to an untrusted network.

The maintained appliance status path is simpler: the playback loop writes the current track to `/var/ramdrive/index.html`, and `mini_httpd` serves that file from RAM.

The original version also contained precise coordinates for weather output. Those have been removed from the public repository and replaced by a placeholder comment.

## Known limitations

- This is a historical Raspberry Pi / PiFM experiment, not a maintained transmitter stack.
- FM transmission is legally regulated and can interfere with other services if misused.
- The scripts use old Raspbian conventions such as `rc.local`, `ifconfig`, and `ifup`.
- The maintained playback path assumes local MP3 files and does not implement a general audio player.
- The web UI and status mechanism are designed for a trusted local network appliance, not a public service.
- Hard-coded paths such as `/radio_nas`, `/pifm`, `/pifmplay`, and `/var/ramdrive/list.txt` reflect the appliance deployment.
- The project remains useful for historical reconstruction and appliance maintenance notes, not for direct modern deployment.

## Image

![RPiFM](www/20150627.JPG)

## Authorship

Project notes, web UI additions, integration work, maintenance notes, and project photograph by Tomáš Gál, unless otherwise noted.

The PiFM-related playback script is based on a historical script by Mikael Jakhelln and should be read as part of this deployment snapshot rather than as a maintained upstream project.

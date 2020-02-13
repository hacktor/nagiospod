# Nagios Pod
Container build with buildah of nagios pod

It has a basic configuration interface for configuring hosts and services and notifications which can be send by email or telegram bot.

## Requirements
* Linux server
* podman
* buildah
## clone the repository and build the container
```bash
git clone https://github.com/hacktor/nagiospod.git
cd nagiospod
./build.sh
podman run -d -p 8080:80 -v ./nagios3:/etc/nagios3 -v ./lib:/var/lib/nagios3 --name nagiospod nagiospod
```

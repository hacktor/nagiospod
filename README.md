# Nagios Pod
Container build with buildah of nagios pod
## Requirements
* Linux server
* podman
* buildah
## clone the repository and build the container
```bash
git clone https://github.com/hacktor/nagiospod.git
cd nagiospod
./build.sh
podman run -d -p 8080:80 -v ./nagios3:/etc/nagios3 --name nagiospod nagiospod
```

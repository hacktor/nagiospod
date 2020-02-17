#!/bin/bash

# assume default cni pod network
CNI_NET=10.88.0.0/16
GATEWAY=10.88.0.1

newcontainer=$(buildah from ubuntu:bionic)

buildah config --created-by "HackTor IT"  $newcontainer
buildah config --author "Ruben de Groot" --label name=nagiospod $newcontainer
buildah config --env CNI_NET=${CNI_NET} --env GATEWAY=${GATEWAY} $newcontainer
buildah copy $newcontainer ./install.sh /usr/bin/install.sh
buildah copy $newcontainer ./entrypoint.sh /usr/bin/entrypoint.sh
buildah copy $newcontainer ./sendtelegram /usr/bin/sendtelegram
buildah copy $newcontainer ./sendsnail /usr/bin/sendsnail
buildah copy $newcontainer ./debconf /root/debconf
buildah run $newcontainer chmod +x /usr/bin/install.sh /usr/bin/entrypoint.sh /usr/bin/sendtelegram /usr/bin/sendsnail
buildah copy $newcontainer ./nagios3 /tmp/nagios3
buildah copy $newcontainer ./lib /tmp/lib
buildah run $newcontainer /usr/bin/install.sh
buildah config --port 80 $newcontainer
buildah config --entrypoint /usr/bin/entrypoint.sh $newcontainer
buildah commit $newcontainer nagiospod:latest

#!/bin/bash

# assume default cni pod network
# todo add $USER3$ and $USER4$ macro's to resource.cfg

newcontainer=$(buildah from ubuntu:bionic)

buildah config --created-by "HackTor IT"  $newcontainer
buildah config --author "Ruben de Groot" --label name=nagiospod $newcontainer
buildah copy $newcontainer ./install.sh /usr/bin/install.sh
buildah copy $newcontainer ./entrypoint.sh /usr/bin/entrypoint.sh
buildah copy $newcontainer ./sendtelegram /usr/bin/sendtelegram
buildah copy $newcontainer ./sendsnail /usr/bin/sendsnail
buildah copy $newcontainer ./debconf /root/debconf
buildah run $newcontainer chmod +x /usr/bin/install.sh /usr/bin/entrypoint.sh /usr/bin/sendtelegram /usr/bin/sendsnail
buildah copy $newcontainer ./nagios3 /tmp/nagios3
buildah copy $newcontainer ./lib /tmp/lib
buildah run $newcontainer /usr/bin/install.sh
buildah config --entrypoint /usr/bin/entrypoint.sh $newcontainer
buildah commit $newcontainer nagiospod:latest

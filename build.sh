#!/bin/bash

newcontainer=$(buildah from ubuntu:bionic)

buildah config --created-by "HackTor IT"  $newcontainer
buildah config --author "Ruben de Groot" --label name=nagios $newcontainer
buildah copy $newcontainer ./install.sh /usr/bin/install.sh
buildah copy $newcontainer ./entrypoint.sh /usr/bin/entrypoint.sh
buildah copy $newcontainer ./debconf /root/debconf
buildah run $newcontainer chmod +x /usr/bin/install.sh /usr/bin/entrypoint.sh
buildah copy $newcontainer ./nagios3 /tmp/nagios3
buildah run $newcontainer /usr/bin/install.sh
buildah config --entrypoint /usr/bin/entrypoint.sh $newcontainer
buildah commit $newcontainer nagiospod:latest

#!/bin/bash

dnsmasq=/usr/local/etc/dnsmasq.d/ec2
dir=$(cd $(dirname $0);pwd)
php $dir/ec2host.php > $dnsmasq

sudo launchctl unload -w /Library/LaunchDaemons/homebrew.mxcl.dnsmasq.plist
sudo launchctl load -w /Library/LaunchDaemons/homebrew.mxcl.dnsmasq.plist
sudo dscacheutil -flushcache

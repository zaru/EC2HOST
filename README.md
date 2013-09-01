EC2HOST
=======

With an IP address of EC2, to rewrite the local hosts file automatically.

EC2HOST is also compatible to dnsmasq either or /etc/hosts.

## Install

	$ cp config.php.sample config.php
	$ vi config.php
	$ git submodule init
	$ git submodule update

## Usage

### /etc/hosts

	$ php ec2host.php host

Reads the /etc/hosts, and then output the string to add the host of EC2 instances. If necessary, please output to /etc/hosts.

	$ sudo sh -c 'php ec2host.php host > /etc/hosts'

### dnsmasq

	$ vi load_dnsmasq.sh
	$ chmod 755 load_dnsmasq.sh
	$ ./load_dnsmasq.sh

The output record file, this script, restart the dnsmasq. Please rewrite it according to the network environment of its own.It will be output to /usr/local/etc/dnsmasq.d/ec2 When it is default.






#!/bin/bash
apt-get -y update >/tmp/startuplog.log
touch /tmp/runupdate
#Install git to get code

git config --global user.email "jeffrey.lee@ptminconline.com"
git config --global user.name "Jeff Lee"

apt-get -y install git puppetmaster puppet>>/tmp/startuplog.log
touch /tmp/installgitpuppet
#create puppet repo

cd /tmp

git clone https://github.com/wealthmentlee/sewealthement.git>>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log
echo "Completed Git Clone">> /tmp/startuplog.log

cp -r /tmp/sewealthement/puppet/dev2/modules/* /etc/puppet/modules/ >>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log
echo "Completed Copy of modules to Default modules">>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log

#Apply puppet manifest
echo "Begin Running Puppet Apply">>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log
puppet apply /tmp/sewealthement/puppet/dev1/manifests/ApachePHP5.pp >>/tmp/startuplog.log
echo "Complete Puppet Apply">>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log
echo "Move website files">>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log
cp -r /tmp/sewealthement /var/www/html/wealthment>>/tmp/startuplog.log
echo "Change Permissions on website folder to 777">>/tmp/startuplog.log
echo "****************************************">>/tmp/startuplog.log
chmod -R 777 /var/www/html/wealthment>>/tmp/startuplog.log

#!/usr/bin/env bash

source /app/vagrant/provision/common.sh

#== Import script args ==

timezone=$(echo "$1")
readonly IP=$2

#== Provision script ==

info "Provision-script user: `whoami`"

export DEBIAN_FRONTEND=noninteractive

info "Configure timezone"
timedatectl set-timezone ${timezone} --no-ask-password

info "AWK initial replacement work"
awk -v ip=$IP -f /app/vagrant/provision/provision.awk /app/environments/dev/*end/config/main-local.php

info "Prepare root password for MySQL"
debconf-set-selections <<< "mysql-community-server mysql-community-server/root-pass password \"''\""
debconf-set-selections <<< "mysql-community-server mysql-community-server/re-root-pass password \"''\""
echo "Done!"

info "Add PHP 8.2 repository"
apt -y install software-properties-common
apt-get upgrade -y | grep -P "\d\K upgraded"

info "Update OS software"
apt-get update
apt-get upgrade -y

info "Add ppa:ondrej/php"
apt-get install -y python-software-properties
apt-get update && apt-get upgrade -y
add-apt-repository -y ppa:ondrej/php

info "Install additional software"
apt-get install -y php8.2 php8.2-curl php8.2-cli php8.2-intl php8.2-mysqlnd php8.2-gd php8.2-fpm php8.2-mbstring php8.2-xml unzip nginx mariadb-server php.xdebug php8.2-zip php8.2-bcmath

info "Configure MySQL"
sed -i "s/.*bind-address.*/bind-address = 0.0.0.0/" /etc/mysql/mariadb.conf.d/50-server.cnf
mysql -uroot <<< "CREATE USER 'root'@'%' IDENTIFIED BY ''"
mysql -uroot <<< "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%'"
mysql -uroot <<< "DROP USER 'root'@'localhost'"
mysql -uroot <<< "FLUSH PRIVILEGES"
echo "Done!"

info "Configure PHP-FPM"
sed -i 's/user = www-data/user = vagrant/g' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = vagrant/g' /etc/php/8.2/fpm/pool.d/www.conf
sed -i 's/owner = www-data/owner = vagrant/g' /etc/php/8.2/fpm/pool.d/www.conf
cat << EOF > /etc/php/8.2/mods-available/xdebug.ini
zend_extension=xdebug.so
xdebug.remote_enable=1
xdebug.remote_connect_back=1
xdebug.remote_port=9000
xdebug.remote_autostart=1
EOF
echo "Done!"

info "Configure NGINX"
sed -i 's/user www-data/user vagrant/g' /etc/nginx/nginx.conf
echo "Done!"

info "Enabling site configuration"
ln -s /app/vagrant/nginx/app.conf /etc/nginx/sites-enabled/app.conf
echo "Done!"

info "Configure php.ini"
#cli
sed -i 's/short_open_tag = Off/short_open_tag = On/g' /etc/php/8.2/cli/php.ini
sed -i 's/;date.timezone =/date.timezone = Europe\/Moscow/g' /etc/php/8.2/cli/php.ini
#fpm
sed -i 's/short_open_tag = Off/short_open_tag = On/g' /etc/php/8.2/fpm/php.ini
sed -i 's/;date.timezone =/date.timezone = Europe\/Moscow/g' /etc/php/8.2/fpm/php.ini
echo "Done!"

info "Initailize databases for MySQL"
mysql -uroot <<< "CREATE DATABASE booking"
mysql -uroot <<< "CREATE DATABASE booking_test"
echo "Done!"

info "Change default php version"
update-alternatives --set php /usr/bin/php8.2
echo "Done!"

info "Install composer"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
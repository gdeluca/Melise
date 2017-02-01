# Melise

Melise : Media Location Information Service
---------------------------------------------
The main idea around Melise is to get information related to a media location
Working with instagram, starting from a media id and trying to identify extra 
information close to the geographical location. The response of the service 
call contain media regional information as detailed: Places from facebook, 
Venues from Foursquare, Medias from Instagram and Business from Yelp.
With Regional I wanted to define a region close to the geographical location.

there are 2 services exposed over rest:
-------------------------------
http://localhost:8000/media/{id}
http://localhost:8000/media/{id}/regional

optional url parameters are:
- distance
- location

System configuration notes
--------------------------
basic installation
sudo apt-get install curl php5 php5-cli phpunit
curl -sS https://getcomposer.org/installer | php

---- updating lib references
php composer.phar update
or the first time
php composer.phar install

download and install composer.phar in project home

to start the web application:
// php -S localhost:8080 -t web web/index.php

running tests with from comamnd line: 
phpunit -c TestSuite.xml.dist
debugger configuration (xdegub)
--------------------------------
git clone git://github.com/xdebug/xdebug.git
cd xdebug/
sudo apt-get install php5-dev
./configure --enable-xdebug
make
sudo make install
vim /etc/php5/cli/php.ini ; edit and add the followings to enable debugger

zend_extension_ts=/usr/lib/php5/20121212/xdebug.so
xdebug.remote_enable = 1
xdebug.remote_host = "127.0.0.1"
xdebug.remote_port = 9000

install chrome xdebug helper


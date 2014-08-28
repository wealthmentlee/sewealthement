#puppet module install example42/php;
#puppet module install puppetlabs/apache;
#puppet module install puppetlabs/mysql"

class {"apache": 
	mpm_module => 'prefork'
}
 
class {"apache::mod::php":
	require => Exec["update"]
}
class { 'php': 
	require => Exec["update"]
}

apache::mod { 'rewrite': }
 
php::module { "mcrypt": }
php::module { "curl": }
php::module { "gd": }
php::module { "xdebug": }
php::module { "mysql": }

apache::vhost{"localhost":
	port	=> 80,
	docroot	=>	"/var/www/html",
}
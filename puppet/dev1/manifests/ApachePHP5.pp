class {"apache": 
	mpm_module => 'prefork'
}
 
apache::vhost{"localhost":
	port	=> 80,
	docroot	=>"/var/www/html",
}
include apache
include apache::mod::php
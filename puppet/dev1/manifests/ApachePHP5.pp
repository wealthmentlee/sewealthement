class { 'apache':
    mpm_module => 'prefork',
}

include apache::mod::php

apache::vhost { 'localhost':
    port => "80",
    docroot => "/var/www/html" 
}
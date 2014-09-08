# install apache2 package
package { 'apache2':
  mpm_module => 'prefork',
  ensure => installed,
}

# ensure apache2 service is running
service { 'apache2':
  ensure => running,
}

# install php5 package
package { 'php5':
  ensure => installed,
}

# install php5 mysql package
package { 'php5-mysql':
  ensure => installed,
}

# install php5 curl package
package { 'php5-curl':
  ensure => installed,
}

# ensure info.php file exists
file { '/var/www/html/info.php':
  ensure => file,
  content => '<?php  phpinfo(); ?>',    # phpinfo code
  require => Package['apache2'],        # require 'apache2' package before creating
} 
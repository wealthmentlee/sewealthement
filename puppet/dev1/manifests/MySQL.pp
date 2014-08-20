include mysql::server

class { 'mysql::bindings':
    php_enable => true,
  }

  package { "mysql-server":
    ensure => present
  }

  service { "mysqld":
    ensure => running,
    enable => true,
    hasstatus => true,
    require => Package["mysql-server"],
  }

  exec { "set mysql root password":
    path => "/usr/bin",
    unless => "mysql -uroot -p$ecretpa$$",
    command => "mysqladmin -u root password $ecretpa$$",
    require => Service['mysql'],
  }



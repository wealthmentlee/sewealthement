include mysql::server

class { 'mysql::bindings':
    php_enable => true,
  }

  exec { "set mysql root password":
    path => "/usr/bin",
    unless => "mysql -uroot -p$ecretpa$$",
    command => "mysqladmin -u root password $ecretpa$$",
    require => Service['mysql'],
  }



include mysql::server

class { 'mysql::bindings':
    php_enable => true,
  }

  exec { "set-mysql-password":
    unless => "mysqladmin uroot -psecretpwd status",
    path => "/usr/bin",
    command => "mysqladmin -uroot password secretpwd",
  }



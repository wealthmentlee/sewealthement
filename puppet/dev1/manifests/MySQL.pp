include mysql::server

  exec { "set-mysql-password":
    unless => "mysqladmin uroot -psecretpwd status",
    path => "/usr/bin",
    command => "mysqladmin -uroot password secretpwd",
  }



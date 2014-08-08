class mysql {
  package { "mysql-server":
    ensure => present,
  }
  service { "mysql":
    ensure => running,
    enable => true,
    hasstatus => true,
    require => Package["mysql-server"], 
  }
  file { "/etc/mysql/my.cnf":
    ensure => present,
    content => template("mysql/my.cnf.erb"),
    notify => Service["mysql"],
    require => Package["mysql-server"],
  }
  exec { "set mysql root password":
    path => "/usr/bin",
    unless => "mysql -uroot -p$ecretpa$$",
    command => "mysqladmin -u root password $ecretpa$$",
    require => Service['mysql'],
  }
}

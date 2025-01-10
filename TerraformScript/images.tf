# Réseau partagé pour tous les conteneurs
resource "docker_network" "shared_network" {
  name = "shared_network"
  lifecycle {
    create_before_destroy = true
  }
}

# Image PHP avec Apache
resource "docker_image" "php_apache" {
  name         = "php:7.4-apache"
  pull_triggers = ["always"]
}

# Conteneur PHP avec Apache
resource "docker_container" "php_web" {
  name  = "php-web-container"
  image = docker_image.php_apache.name

  ports {
    internal = 80
    external = 8080
  }

  volumes {
    host_path      = "C:/xampp/htdocs/PROJET/biblio/app"
    container_path = "/var/www/html"
  }

  networks_advanced {
    name = docker_network.shared_network.name
  }

  command = [
    "bash",
    "-c",
    "apt-get update && apt-get install -y libmariadb-dev && docker-php-ext-install pdo pdo_mysql && apache2-foreground"
  ]

  lifecycle {
    ignore_changes = [image, volumes]
  }
}

# Image MySQL
resource "docker_image" "mysql_image" {
  name         = "mysql:5.7"
  pull_triggers = ["always"]
}

# Conteneur MySQL
resource "docker_container" "mysql_db" {
  name  = "mysql-container"
  image = docker_image.mysql_image.name

  ports {
    internal = 3306
    external = 3306
  }

  env = [
    "MYSQL_ROOT_PASSWORD=root",
    "MYSQL_DATABASE=biblio",
    "MYSQL_PASSWORD=root"
  ]

  networks_advanced {
    name = docker_network.shared_network.name
  }

  volumes {
    host_path      = "/path/to/mysql/data" # Données persistantes
    container_path = "/var/lib/mysql"
  }

  lifecycle {
    ignore_changes = [image, env]
  }
}

# Image phpMyAdmin
resource "docker_image" "phpmyadmin_image" {
  name         = "phpmyadmin/phpmyadmin"
  pull_triggers = ["always"]
}

# Conteneur phpMyAdmin
resource "docker_container" "phpmyadmin" {
  name  = "phpmyadmin-container"
  image = docker_image.phpmyadmin_image.name

  ports {
    internal = 80
    external = 8081
  }

  env = [
    "PMA_HOST=mysql-container",
    "PMA_PORT=3306",
    "PMA_USER=root",
    "PMA_PASSWORD=root"
  ]

  networks_advanced {
    name = docker_network.shared_network.name
  }

  depends_on = [docker_container.mysql_db]

  lifecycle {
    ignore_changes = [image, env]
  }
}

# Image Grafana
resource "docker_image" "grafana_image" {
  name         = "grafana/grafana:latest"
  pull_triggers = ["always"]
}

# Conteneur Grafana
resource "docker_container" "grafana" {
  name  = "grafana-container"
  image = docker_image.grafana_image.name

  ports {
    internal = 3000
    external = 3000
  }

  env = [
    "GF_SECURITY_ADMIN_PASSWORD=admin"  # Mot de passe administrateur Grafana
  ]

  networks_advanced {
    name = docker_network.shared_network.name
  }

  depends_on = [docker_container.php_web, docker_container.mysql_db]

  lifecycle {
    ignore_changes = [image, env]
  }
}


# Nouveau Conteneur PHP avec Apache pour un autre module
resource "docker_container" "php_web_module2" {
  name  = "php-web-container-Info"
  image = docker_image.php_apache.name

  ports {
    internal = 80
    external = 8082
  }

  volumes {
    host_path      = "C:/xampp/htdocs/bibli/Biblio/Info"
    container_path = "/var/www/html"
  }

  networks_advanced {
    name = docker_network.shared_network.name
  }

  command = [
    "bash",
    "-c",
    "apt-get update && apt-get install -y libmariadb-dev && docker-php-ext-install pdo pdo_mysql && apache2-foreground"
  ]

  lifecycle {
    ignore_changes = [image, volumes]
  }
}

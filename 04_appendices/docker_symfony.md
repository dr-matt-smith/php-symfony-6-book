
# Docker and Symfony projects

## Setup
Start with your Symfony project directory

## Dockerfile
Create a file `Dockerfile`, containing the steps to build a Docker Image of your virtual computer system.


This `Dockerfile` assumes your Symofny project code is in directory "admin-prototype":

```
    FROM php:7.1.7-apache

    COPY admin-prototype /var/www

    ## Expose apache.
    EXPOSE 80

    ## Copy this repo into place. - if /www/site is referred to in Apache conf file ...
    #ADD admin-prototype/web /var/www/site

    ## Update the default apache site with the config we created.
    ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

    ####### fix Symfony var Cache issue ################
    # source: http://symfony.com/doc/current/setup/file_permissions.html
    CMD  HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)
    CMD setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX /var/www/var
    CMD setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX /var/www/var

    ## Run symfony server
    CMD php /var/www/bin/console server:run 0.0.0.0:80&
```

## Build your Docker image
Build your Docker image:

```bash
    $ docker build -t my-application .
```

## Run a Container process based on your image (exposing HTTP port 80)
Now run your Docker **Image** as a Docker **Container** process. The `-p 80:80` option is to expose port 80 in the container as port 80 on your main computer system, so you can visit the web site via your web browser at ` http://localhost`.

```bash
    docker run -it -p 80:80 my-application
```


## Alternative `Dockerfile` for a basic PHP application, using Apache

This `Dockerfile` assumes the PHP project files are in directory "game1":

```
    FROM php:7.1.7-apache

    COPY game1 /var/www

    ## Expose apache.
    EXPOSE 80

    ## Copy this repo into place. - if /www/site is referred to in Apache conf file ...
    #ADD game1/web /var/www/site

    ## Update the default apache site with the config we created.
    ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf

    ## By default start up apache in the foreground, override with /bin/bash for interative.
    CMD /usr/sbin/apache2ctl -D FOREGROUND
```


## Create config file for Apache
Create a file `apache-config.conf`, containing the following:

```
    <VirtualHost *:80>
      ServerAdmin me@mydomain.com
      DocumentRoot /var/www/web

      <Directory /var/www/web/>
          Options Indexes FollowSymLinks MultiViews
          AllowOverride All
          Order deny,allow
          Allow from all
      </Directory>

      ErrorLog ${APACHE_LOG_DIR}/error.log
      CustomLog ${APACHE_LOG_DIR}/access.log combined

    </VirtualHost>
```

## Other Docker reference stuff

### Docker Images

List images on disk

```bash
    docker images
```

See the details of an image:

```bash
    docker history php:7.1-cli
```

(hint: ` php:7.1-cli` = repository name : tag)

### Containers
List currently running containers (processes):

```bash
    docker ps
```

Run an image as a container process:

```bash
    docker run -it repository:tag /bin/bash
```

Note:

- the "-it" means go into an INTERACTIVE TERMINAL

- the "/bin/bash" is the command to run - i.e. run our BASH shell

Kill a process:

```bash
    docker kill NAME
```

E.g. if container name was `wonderful_wozniak` then you'd type:

```bash
    docker kill wonderful_wozniak
```

### New Image from current (changed) state of a running Container
To Save an updated filesystem in Container to a new Image do the following:

```bash
    docker commit -m "comments" containerName
```

E.g. if container name was `nifty_hodgkin` and you'd installed, say, git and composer, then write:

```bash
    $ docker commit -m "installed git and composer" nifty_hodgkin
    sha256:7e555cc0df651a1b68593733a35cdac341175bed294084eb73b7fb23ebdc5bbd
    $
```

Note that the SHA is output, the ID of the new Image.

You can then add a **tag** to the new image.

First look at the images, and note its short Image Id:

```
    $ docker images
    REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
    <none>              <none>              7e555cc0df65        5 days ago          433 MB
    phpd                latest              d0ee7be93033        5 days ago          372 MB
```

Now  give a TAG to our image, e.g. `php_composer_git`:

```bash
    $ docker tag 7e555cc0df65 php_composer_git
```


Now we see our nicely tagged Image:

```bash
    $ docker images
    REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
    php_composer_git    latest              7e555cc0df65        5 days ago          433 MB
    phpd                latest              d0ee7be93033        5 days ago          372 MB
```

### Exposing HTTP ports for Containers running web application servers
We can use the option `-p PORT:PORT` to expose a port from the Container to our main conputer system.

E.g. To expose Container port 80 as port 80 on our computer we add `-p 80:80`, as part of our `docker run` command:

```bash
    $ docker run -it -p 80:80 php_composer_git /bin/bash
```

We can **Inspect** the details of a running Container with the `docker inspect` command:

```bash
    docker inspect wonderful_wozniak
```

## Useful reference sites

Some useful sites for Docker and PHP include:

- (Good overview)[http://odewahn.github.io/docker-jumpstart/docker-images.html]
- (Web Server Docker - with note about Mac IP)[https://writing.pupius.co.uk/apache-and-php-on-docker-44faef716150]

- (Nice intro for PHP)[https://semaphoreci.com/community/tutorials/dockerizing-a-php-application]

From the offical Docker documentation pages:

- (Introduction)[https://docs.docker.com/get-started/#conclusion]
- (Download Docker)[https://www.docker.com/community-edition#/download]



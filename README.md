rfGallery is a very simple photos gallery written in PHP.
It only uses the filesystem to know which photos to show, no database
is used. 

It generates thumbnails for you but you can generate the thumbnails by
yourself in advance. The presence of a thumbnail is checked before
generating one. Thus, the generation (if any) occurs only once for each thumbnail.

A sample script to recursively generate photos thumbnails using
imagemagick is provided in the `tools/` folder.

It uses the jQuery plugin yoxview <http://www.yoxigen.com/yoxview/> to
display slideshow of your photos.

Some graphic files used come from the Open Icon Library <http://openiconlibrary.sourceforge.net/>

Thumbnail generation is powered by Imagick php module.

Requirements
------------
A web server with php5+ installed and working (tested up to php7). The imagick module
must be installed and enabled for thumbnail generation to work properly.

You can also use docker with the provided docker file.

Installation with apache
------------------------

You simply have to put it in a virtual host with index.php as index

Here is an example of apache2 configuration for this tool to work:

        <VirtualHost *:80>
            ServerAdmin webmaster@fairy-project.org
            ServerName www.fairy-project.org
            ServerAlias www
        
            DocumentRoot /srv/www/photos
            DirectoryIndex index.php
        
            <Directory /srv/www/photos>
                Options -Indexes FollowSymLinks MultiViews
                AllowOverride Indexes
                Order allow,deny
                allow from all
            </Directory>
        
            ErrorLog /var/log/apache2/rfgallery-error.log
        
            # Possible values include: debug, info, notice, warn, error, crit,
            # alert, emerg.
            LogLevel warn
        
            CustomLog /var/log/apache2/rfgallery-access.log combined
            ServerSignature Off
        </VirtualHost>

Installation with docker
------------------------

Build the image using the provided `Dockerfile`

    $ docker build -t rfgallery tools/docker

The image uses two volumes:
    * `/var/www/html/photos` for putting your photos
    * `/var/www/html/thumbs` where the thumbnails will be generated
      (you can generate yourself using an external tool)

The image will use the default configuration file. If you want to
customize it, you can simply do a bind mount to
`/var/www/html/config.php` when running a container.

For example, to run a container that will listen on the host's tcp port 80:

    $ docker run --rm -v /path/to/your/photos:/var/www/html/photos \
      -v /path/to/your/thumbnails:/var/www/html/thumbs -v /path/to/config.php:/var/www/html/config.php:ro \
      -p 80:80 -d rfgallery

If you use `docker-compose`, you can adapt this:

        version: '2'
        services:
          traefik:
            image: traefik
            command: --api --docker
            environment:
              TZ: "Europe/Paris"
            ports:
              - "80:80"
              - "8080:8080"
            volumes:
              - "/var/run/docker.sock:/var/run/docker.sock"
          photos:
            image: rfgallery
            environment:
              TZ: "Europe/Paris"
            volumes:
              - "photos:/var/www/html/photos"
              - "thumbs:/var/www/html/thumbs"
              - "./config.php:/var/www/html/config.php:ro"
            labels:
              - "traefik.frontend.rule=Host:photos.example.com"
              - "traefik.port=80"
        volumes:
          photos:
          thumbs:
        
This examples shows how you can use traefik as reverse proxy (check
traefik documentation to see how you can benefit of automatic letsencrypt
certificate generation for https)

Configuration
-------------

All you have to do is to setup the photo and thumbnails folder in the
config.php (a config.php.sample file is given for reference)

        $PHOTOS_DIR = "photos";
        $THUMBS_DIR = "thumbs";
        
The path is relative to the document root

Folders are treated as albums and, as some tests are base on
filenames, every `.jpg` file in a folder is a photo of the album. The
thumbnail folder architecture and filenames must be equivalent to the
photo one. If a photo is available under
`$PHOTOS_DIR/album1/subalbum1/myphoto.jpg`, its thumbnail must be
stored in `$THUMBS_DIR/album1/subalbum1/myphoto.jpg`. If the thumbnail
does not exists, it is created (check folder permissions, the
webserver must have write access)


You can also modify the name of the gallery home page in the same file.
        
        define(HOME_PAGE_NAME, "Photos");

This name is used for the page titles and as the first navigation link.

You can change the copyright notice by changing the define:
        
        define(FOOTER_MESSAGE, "Gallery provided by <a href=\"http://github.com/hauspie/rfgallery\">rfGallery</a>");


Security
--------
No authentication mecanism is provided with rfGallery. If you wan't to limit
access to your gallery, you must use apache authentication mechanism.
You can add a `.htaccess` file at the document root.
For example:

        AuthName rfGallery
        AuthUserFile /var/www/html/.htpasswd
        AuthType Basic
        require valid-user

and use `htpasswd` to create the `.htpasswd` file.  Please note that if
you do not use https, login and password are transmitted in plain text!

If you use docker-compose and traefik as reverse proxy, you can add a traefik label to the rfgallery service:

        - 'traefik.frontend.auth.basic=login:$5$salt$Gcm6FsVtF/Qa77ZKD.iwsJlCVPY0XSMgLJL0Hnww/c1'

The password hash can be generated using the crypt call (man crypt), for exemple in perl

        perl -e 'print crypt("password", "\$5\$salt\$\$") , "\n"'

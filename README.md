# zeal

This is what the httpd-vhosts.conf should be to render the website in the development environment (Comment out when need to access phpmyadmin)
```
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot "C:/xampp/htdocs/zeal/web"
    DirectoryIndex app_dev.php
    <Directory "C:/xampp/htdocs/zeal/web">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride None
        Order allow,deny
        allow from all
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ /app_dev.php [QSA,L]
        </IfModule>
    </Directory>
</VirtualHost>
```

Useful command to clear cache. Remember bin/console not app/console
```
$ php bin/console cache:clear
```

Lists all routes
```
php bin/console debug:router
```

Using FOSUserBundle for the website login/registration etc but custom system for the API login/registration etc.


## To enable debug() add this to php.ini
```
auto_prepend_file = ${HOME}/.composer/vendor/autoload.php
```

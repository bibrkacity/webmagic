<h2>Test task for Yuriy Maksymenko </h2>



## Task details

https://bitbucket.org/webmagic/testing/src/php-dev/TestTask.md


## How to install application 

You need to have installed <a href="https://getcomposer.org/download/">composer</a> on your server. Also you need Internet-connection for server. 

1. Clone application from repository:

https://github.com/bibrkacity/webmagic.git

2. Set active directory as folder web of application's folder - in terminal. For example:

cd ~/www/webmagic/web

3. Run command in terminal:

composer update

3. Save  file web/.env.example as web/.env and edit parameters of MySQL-connection in web/.env. For example:

<pre>DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webmagic
DB_USERNAME=webmagic
DB_PASSWORD=test</pre>

4. Run migrations in terminal:

php artisan migrate

<p style="font-style:italic;margin-left:2em">If you have error "class not found" - try to run <br />composer dump-autoload</p>

5. Run built-in Web-server:

php artisan serve

6. Go to http://127.0.0.1:8000

If error because application key - run:

php artisan key:generate

7. Enjoy!





# Setup Instructions

## Requirements
1. All of laravel 5.6 basic requirements
2. Create a database and specify the database parameters in the .env file
e.g
* DB_CONNECTION = mysql
* DB_HOST = 127.0.0.1
* DB_PORT = 3306
* DB_DATABASE = myDatabaseName
* DB_USERNAME = myDbUsername
* DB_PASSWORD = myDbPassword


## Run the following via Command line or terminal
1. composer install
2. php artisan key:generate
3. php artisan migrate
4. php artisan db:seed
5. php artisan serve

Visit site via the url http://localhost:8000

### Default login
* Email: admin@admin.com
* password: admin

The login credentials can be changed after login 

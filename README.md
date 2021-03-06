# Welcome to ToDo&Co : keep track of your daily tasks !


Codacy's analysis : [![Codacy Badge](https://app.codacy.com/project/badge/Grade/94ca4af2b6774c28970281223375c741)](https://www.codacy.com/gh/CharB-22/OC-Todo-Co/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=CharB-22/OC-Todo-Co&amp;utm_campaign=Badge_Grade)

. For more information go to : https://openclassrooms.com/fr/paths/59/projects/44/assignment

## Tables of Contents
  * [Repository Content](#repository-content)
  * [Technologies](#technologies)
  * [Set Up](#set-up)
  * [Testing](#testing)

## Repository content
  * The application pages and folders needed to run the application
  * The composer.json needed to install the libraries used for this project

## Technologies
  * PHP 7.4.1
  * Symfony 5.3.6
  * Webpack Encore 5.38.1

## Set Up
  * Clone or download the github project
  ```
  git clone https://github.com/CharB-22/OC-Todo-Co.git
  ```
  * Make sure you have Composer installed on your computer, as it is needed to install any packages. If this is not the case, follow the directions on the composer website to dowload it: https://getcomposer.org/download/ 
  
  * Install the needed libraries via composer with the command below:
  ```
  composer install
  ```
  * For this application, you need to have MySQL to manage your database - usually available if you are using MAMP or WAMP Server locally. Create an .env.local file if you run the website locally in order to update the database url. Add this link to the file :
  ```
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
  ```
  Update db_user, db_password and db_name with your MySQL credentials and a name for the database of this project.
  An alternative is to just update this link directly into the .env file - however, make sure to remove # in front of the link, and update only the mysql one.

  * Create the database via your command line :
  ```
  php bin/console doctrine:database:create
  ```
  * Import the structure of the database thanks to the migrations in the project :
  ```
  php bin/console doctrine:schema:create
  ```
  * Populate the database with some datas if you want to have a look and feel.
  ```
  php bin/console doctrine:fixtures:load
  ```
  * Last but not least, this project is using WebPack Encore for the css and js files integration. Webpack Encore needs npm and the node_modules folder (exclude by .gitignore) locally to be able to work. To do so, you just need to run the command below:
  ```
  npm install
  ```

  * Start your server to go to the website demo:
  ```
  php -S localhost:8000 -t public
  ```
  
  ## Testing
  
  Tests have been written for the application. In order to be able to run them locally, you need to create an .env.test.local file and follow the same logic as the set-up :
  ```
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
  ```
  Update db_user, db_password and db_name with your own credentials and a name for the database for this project.
  
  In the tests already written, the creation of the database is specified in the setUp() method. If you still wanted to manually create the test database, the command is the same,   but the key is to indicate which environment you want it to be created:
  
  ```
  php bin/console doctrine:database:create --env=test
  ```
  
  To run the tests, use the command below:
  ```
  vendor/bin/phpunit
  ```
  
  And finally, in order to check the test coverage :
  ```
  vendor/bin/phpunit --coverage-html web/test-coverage
  ```
  
  The final dashboard will be visible in /web/test-coverage/index.html
  
  
Happy coding !

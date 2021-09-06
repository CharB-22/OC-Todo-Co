# How to contribute


Do you want contribute to the project ? Please, follow the steps below in order to help us on ToDo&Co !

## Step 1: Fork this repository
Create a fork of this project - you will find the icon on the top of this page. This will create a copy of this repository in your github account.

## Step 2: Clone this repository
Now, clone the forked repository to your local computer. 
In your Github account, go to the forked repository, click on the "Code" button and then click "Copy to clipboard" icon.

Then, go to your terminal and write the command below:
  ```
  git clone https://github.com/CharB-22/OC-Todo-Co.git
  ```


## Step 3: Install the project on your local environment

  * Install the needed libraries via composer
  ```
  composer install
  ```
  * Create an .env.local file if you run the website locally in order to update the database url. Add this link to the file :
  ```
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
  ```
  Update db_user, db_password and db_name with your own credentials and a name for the database for this project.
  An alternative is to just update this link directly into the .env file - however, make sure to remove # in front of the link, and update only the mysql one.

  * Create the database via your command line :
  ```
  php bin/console doctrine:database:create
  ```
  * Import the structure of the database thanks to the migrations in the project :
  ```
  php bin/console doctrine:database:create
  ```
  * Populate the database with the datas used to test
  ```
  php bin/console doctrine:fixtures:load
  ```

## Step 4: Create a new branch

Create a new branch to start adding your own code :

  ```
  git checkout -b your-new-branch-name
  ``` 
 You can start to work on this branch newly created.

 ## Step 5: Make necessary changes and run your tests

 Before commiting your changes, make sure the tests already created are still ok, that your code didn't break the existing code of the application.
 To run the tests, make sure xdebug is enabled in your php.ini and use the command below:
  ```
  php bin/phpunit
  ``` 
If you have created any new functionnalities, controllers, entities,... add your tests to the current ones before submitting your work.
Once you have the green light after running the tests, you can commit your changes.

  ```
  git commit -m "Explicit message about your changes"
  ``` 


## Step 6:  Push changes to Github

Push your changes using the command git push:
  ```
  git push origin <add-your-branch-name>
  ``` 
replacing <add-your-branch-name> with the name of the branch you created earlier.

## Step 7:  Push changes to Github
Go to your repository on Github to see that the button <Compare & pull request> has appeared. Click on that button to submit your pull request.

Once the pull request has been submitted, the team will have a look before merging your work to the main branch.

Happy coding !

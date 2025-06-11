# Junior Jazz Portal
Junior Jazz Order Portal is an online website for ordering pre-existing sets of jerseys or custom ones .
## Sofware required
* Node.js 
* PHP
* Composer


## Steps to setup the projects
Please follow these steps to set up your project locally.
<br>
1. <img src="https://img.icons8.com/ios/20/000000/code-fork.png"/> **Fork** the <b>_junior-jaaz-order-portal_</b> project into your account repository.
2. <img src="https://img.icons8.com/external-flat-icons-inmotus-design/20/000000/external-clone-clone-flat-icons-inmotus-design-2.png"/> **Clone** the <b>_junior-jaaz-order-portal_</b> repository.

```
https://github.com/shikhartech/junior-jaaz-order-portal.git
```

OR

```
git@github.com:shikhartech/junior-jaaz-order-portal.git
```

3. Navigate to the project directory. Skip if you are currently on the project directory.
```
cd <project_name>
````
4. Execute the following command to install **Composer**:
```
composer install
```
> The _**Composer Install**_ command installs all the required project dependencies. 
5. Execute the following command to install **NPM**:
```
npm install
```
6. Duplicate the `.env.example` and then rename into `.env` by running this command: 
```
cp .env.example .env
```
7. Generate an app encryption key by running this command:
```
php artisan key:generate
```
> The _**php artisan key:generate**_ command generate an app key amd stores in your **.env** file. 
8. It's time to set up a _**database**_. You can use any database management tool you like to create it.
9. After setting up a new _**database**_, enter the database information in the _**.env**_ file.
```
DB_DATABASE =
DB_USERNAME =
DB_PASSWORD =
```
> If you're using a different database driver (e.g., MySQL), you must change the _**DB_CONNECTION**_ value accordingly.
10. Execute the following command to perform _**data migration**_.
```
php artisan migrate
```
>_**Php artisan migrate**_ executes database migrations which syncs database structure withe the application's model. 

11. Run the following command and ensure it remains running [Do not close it]:
```
php artisan serve
```
> Copy the URL generated after executing the command and paste it into the .env file.
```
APP_URL= <url_generated_by_the_command>
```
12. Finally, execute the following command in a terminal different from where you are running _**php artisan serve**_.
```
npm run dev
```
Job well done! You have successfully set up the project on your local machine. You can now proceed with making the necessary changes as per the task.

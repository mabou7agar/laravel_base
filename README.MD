## Installation

### Composer Require
``` bash
composer require m-tech-stack/base-package
```
### Run Vendor
``` bash
php artisan vendor:publish --tag="base-package" --force
```

### How To Use

#### Create New Module
``` bash
php artisan module:make ModuleName
```

#### Create New Module inside Folder
- Main Module ( Ex. Payment )
- Sub Module ( Urway )
``` bash
php artisan module:make ModuleName MainModule
```

#### Create Migration inside module assuming Module is User
``` bash
php artisan module:make-migration create_users_table User
```

#### You can follow this structure for any other supported artisan commands
``` bash
php artisan module:make-{artisan-action} ModuleName
```

#### Similar to
``` bash
php artisan make:{artisan-action}
```

## Structure

All application files will be under app folder we have the following
1. **Console**
    1. Contains Custom Commands to be called like php artisan ScrapeHotelsCommand
    2. Contains Kernel that where we can configure schedules and import the commands

2. **Data** - it contains Data Classes
    * Data classes are used to return well-structured data for a payload.
    * Data classes are very useful to transfer data between the system components in a maintainable way

3. **DTOs**
    * DTO (Data Transfer Object) is a simple utility and a small component that can be used to transfer data
      between the outside world to the inside world of either the Domain or the Module.

4. **Exception** - Here where we create our custom exceptions

5. **Handlers** - Used to make an action without need any feedback delete action for example or run a statement

6. **Http**
    1. Controllers ( Contains the controllers that use to handle outside requests and the presentation of data and communicating with routes,services to make the request )
    2. Middleware ( Convenient mechanism for inspecting and filtering HTTP requests entering the application like how we use it at Auth )
    3. Requests ( Where we create the expected requests and define their rules )
    4. Kernel ( The place where we define the middlewares )

7. **Jobs** - Here we create the jobs needed to be dispatched in the Queues.

8. **Models** - Contains all of your Eloquent model classes

9. **Presenters**
    * Instead of outputting all the model fields and their loaded relationship,
    * We can basically format that to an agreed structure that will output only the needed data and also rename fields
    * To be more convenient than the original names in models or datasets
    * We use AbstractPresenter to make Presenter support both Collection or single item

10. **Providers** - Service providers are the central place to configure the application

11. **Repositories** - provides an abstraction layer between the application logic and the data persistence layer
    * We can say this is a middle man the handle the communication between Services and the Models

12. **Services** - Services are the basic classes to run logic.

13. **Database**
    1. Migration - Migrations are like version control for your database, allowing your team to modify and share the application's database schema
    2. Factories - define database records in a predictable and easily replicable way
    3. Seeders - the ability to seed your database with data using seed classes

14. **Routes** - The route is a way of creating a request URL for your application

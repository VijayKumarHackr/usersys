# UserSys
Configurable user management system for Laravel 5.

* [Introduction](#introduction)
* [Installation](#installation)
* [Updating `User` Eloquent Models](#user-model)
* [Routes](#routes)
* [Requests](#requests)
* [Events](#events)
* [Listeners](#listeners)
* [Migrations](#migrations)
* [Assets](#assets)
* [Translations](#translations)
* [Views](#views)
* [Configuration](#config)
* [Copyright and License](#copyright)

> **NOTE** The package is built keeping Laravel 5 in mind and has not been tested for Laravel 4 or below.

<a name="introduction"></a>
## Introduction: Why use UserSys?
Laravel comes with a user system out of the box but it is not so much configurable as one might expect. UserSys is the package for Laravel 5 developers who want to get a proper hold over the user system mechanism.

You can configure or customize, major features of the user system just my making minor edits or by setting appropriate values to the flags in the configuration file.

Views are given out of the box and of course you can replace them with your own, just by defining them in the config file.

<a name="installation"></a>
## Installation

To install UserSys as a Composer package to be used with Laravel 5, simply run:
`composer require qumonto/usersys`

Next, update `config/app.php` by registering the service provider of UserSys package.

```php
'providers' => [
    // ...
    Qumonto\UserSys\UserSysServiceProvider::class,
];
```

From the command line, run `php artisan vendor:publish` to publish the default configurations, views, migrations, translations, events, listeners, assets and requests. If don't want to publish all the files then you may follow the command with appropriate tags.

Example: `php artisan vendor:publish -tag="config"` can be used to publish only configuration file.

> **NOTE** For details about the tags for each category of files visit the corresponding section in the README.md

<a name="user-model"></a>
## Updating `User` Eloquent Model
The `User` model's `fillable` array must be updated with a value `verification_token` to avoid mass assignment exception while registering a new user record into the database.

<a name="routes"></a>
## Routes
All the routes required for user system are added out of the box and the list of those routes are given below:

| Method   | URI                   | Action                                                       | Middleware |
|----------|-----------------------|--------------------------------------------------------------|------------|
| POST     | login                 | Qumonto\UserSys\Http\Controllers\AuthController@postLogin    | guest      |
| GET 	   | login                 | Qumonto\UserSys\Http\Controllers\AuthController@getLogin     | guest      |
| GET 	   | logout                | Qumonto\UserSys\Http\Controllers\AuthController@getLogout    |            |
| POST     | register              | Qumonto\UserSys\Http\Controllers\AuthController@postRegister | guest      |
| GET 	   | register              | Qumonto\UserSys\Http\Controllers\AuthController@getRegister  | guest      |
| POST     | reset                 | App\Http\Controllers\Auth\PasswordController@postReset       | guest      |
| POST     | reset/email           | App\Http\Controllers\Auth\PasswordController@postEmail       | guest      |
| GET 	   | reset/email           | App\Http\Controllers\Auth\PasswordController@getEmail        | guest      |
| GET 	   | reset/{token}         | App\Http\Controllers\Auth\PasswordController@getReset        | guest      |
| GET	   | verify/{verify_token} | Qumonto\UserSys\Http\Controllers\AuthController@getVerify    | guest      |

> **NOTE** You cannot overide the routes, but in futher releases routing will be made flexible.

<a name="requests"></a>
## Requests
  * `UserLoginRequest` validates the login requests by the users. By default, the rules are defined in the `rules()` method of the `UserLoginRequest` class which says, an email/username is required and must be less than '255' characters. And the password is also required and must be at least '5' characters long.

  * `UserRegisterRequest` validates the registration requests by the user. By default, the username/email field is marked required with less than '255' characters and should be a unique value. The password must be atleast '5' characters and should be confirmed.

  Adding a `requests` tag to vendor:publish will publish the requests classes.
  `php artisan vendor:publish --tag="requests"`

 > **NOTE** You may modify the rules according to the needs of your project. Request files are located in `app/Http/Requests/` directory.

<a name="events"></a>
## Events
An event will be fired when new user is registered. The event class is named `UserRegistered` and extends `App\Events\Event`.

To push the `UserRegistered` event class to the `app/Events` directory, use `events` tag.
`php artisan vendor:publish --tag="events"`

<a name="listeners"></a>
## Listeners
We have included a listener class `AccountVerificationEmail` which sends an account verification email to the registered users if the `email_verification` flag is set to `true` in `config/usersys.php` file.

To publish the listener to `app/Listeners` directory, use `listeners` tag with the vendor:publish command.
`php artisan vendor:publish --tag="listeners"`

<a name="migrations"></a>
## Migrations
You don't really have to use the migrations provided by this package, you may skip publishing migrations by adding/modifying 4 simple attributes to your `users` table.

Here are the added/modified attributes that UserSys package's `users` table migration provides by default:
```php
$table->string(config('usersys.username_column_and_field'))->unique();
$table->string(config('usersys.password_column_and_field'), 60);
$table->string('verification_token', 50)->nullable();
$table->rememberToken();
```
`$table->string(config('usersys.username_column_and_field'))->unique();` will hold the email or username or any other field depending on how you've set `username_column_and_field` flag in your `config/usersys.php` file.

`$table->string(config('usersys.password_column_and_field'), 60);` will be used to hold the encrypted password. The name of the attribute used to store user's password, depends upon how you've configured `password_column_and_field` flag in `config/usersys.php` file.

`$table->string('verification_token', 50)->nullable();` will contain the email verification token that will be used in account verification process. Remember, the `verification_token` attribute must be at least 50 characters in length.

`$table->rememberToken();` will be used to store a token for "Remember me" sessions being maintained by your application. Hence you must verify that your `users` table contains the following column if you wish to provide "Remember me" functionality.

<a name="assets"></a>
## Assets
The assets include bootstrap css file with the name `app.css` which will be referenced in the views that are provided by default in UserSys package. The assets will be published to `/public/css/` directory.

To publish the assets available with this package you may run `php artisan vendor:publish --tag="assets"`.

<a name="translations"></a>
## Translations
Translations include language files which will be used to display messages to the user for a certain activity.
For example, if can edit the `email_verification_subject` flag to change the subject of the verification email sent to the user.

To publish the translations into `app/resources/lang/en` directory, you may run the following command:
`php artisan vendor:publish --tag="translations"`.

<a name="views"></a>
## Views
Since, UserSys uses the default `PasswordController` class for password resets, views are handled differently.
The views `password.blade.php` (forgot password view) and `reset.blade.php` (account reset view) will be pushed to `resources/views/auth/` directory by running `php artisan vendor:publish --tag="password-views"`.

Other views such as `login.blade.php`, `register.blade.php` and `verify.blade.php` can be configured in the `config/usersys.php` file.

<a name="config"></a>
## Configuration

|	Configuration flags		|		Defaults		|						Examples					|
|---------------------------|-----------------------|---------------------------------------------------|
|	`app_name`				|	   'UserSys'		|	`'app_name'	=> 'UserSys',`						|
|	`redirect_after_fail`	|	  	'login'			|	`'redirect_after_fail' => '/',`					|
|	`redirect_after_logout`	|	  	'login'			|	`'redirect_after_logout' => 'login',`			|
|	`redirect_after_login`	|	  	'home'			|	`'redirect_after_login' => 'dashboard',`		|
|	`redirect_after_verification`|	'login'			|	`'redirect_after_verification'	=>	'welcome',`	|
|	`log_user_after_registration`|	`false`			|	`'log_user_after_registration' => true,`		|
|	`email_verification`	|	   	`true`			|	`'email_verification' => false,`				|
|	`username_column_and_field`	|	'email'			|	`'username_column_and_field' => 'username',`	|
|	`password_column_and_field`	|	'password'		|	`'password_column_and_field'	=> 'passwd',`	|
|	`login_view`			|		`null` 			|	`'login_view'	=>	'auth.login',`				|
|	`register_view`			|		`null` 			|	`'register_view'	=>	'auth.register',`		|
|	`email_verify_view`		|		`null` 			|	`'email_verify_view'	=>	'emails.verify',`	|
|	`session_identifier`	|		'message'		|	`'session_identifier'	=> 'flash-message',`	|

#### `app_name`
Here, you may specify the name of your application and it will be visible in the navbar header in everypage.

#### `redirect_after_fail`
When a user attempts to login to the application and if the user fails to authenticate then the user will be redirected to the route set to this flag. Default is set to 'login' route but you are free to change that.

#### `redirect_after_logout`
When the user logs out of the application, the user will be redirected to the route set to this flag, after a successful logout. Default is set to 'login', can be set according to preference.

#### `redirect_after_login`
After a successful login into the application, the user will be redirected to the route set to this flag. The default is set to 'home' and can be changed according to the preference.

#### `redirect_after_verification`
Once the user completes email verification process, the user will be redirected to the route set to this flag. The default is set to 'login', but if you wish to omit the email verification feature then this field will not affect your application.

#### `log_user_after_registration`
By setting this flag (either `true` or `false`) you can decide whether or not to allow the user to login into their account. By default the value is set to false, hence the user requires to provide email/username and password to login after a successful registration. If the value is set to `true`, then after a successful registration, the user will be redirected to the path set to `redirect_after_login` key. Else, user will be redirected to the path set to `redirect_after_logout`.

#### `email_verification`
By setting this flag, either `true` or `false`, you can enable or disable the email verification feature.
If the value is set to true, then after a successful registration, the user will have to verify the account by clicking a link sent via email. Otherwise, user will be able to login to the account without any email verification process.

> **NOTE** Remember, the fillable array in the 'User' model must be updated with `verification_token` field.

#### `username_column_and_field`
The name field of login email at login form and the corresponding database column must be the same. Therefore, if you are using 'email_id' as the name for email field in login form, then you must also use 'email_id' as the name for the corresponding database column. Setting this flag will make sure that once it's specified the name can be accessed at both the scenarios.

#### `password_column_and_field`
If you're using a different identifier, other than 'password', in your database to store user's password then you may specify it here and the rest will be taken care. Futher, if you are replacing the default views then make sure you name the password field in the HTML page accordingly.

#### `login_view`
You may specify the view you want to use for login form. It will be overiding the default view of the package. If you want to load the default views for login page then set the field to 'null'.

If your login view is located inside `/resouces/views/auth/` and named `login.blade.php` then you may set the flag as follows:
```php
'login_view'	=>	'auth.login',
```

#### `register_view`
Here, you may specify the view you want to show for register form. It will be overiding the default view of the package. If you want to load the default views for register page then set the field to 'null'.

If your registration page view is located at `/resources/views/auth/` and named to be `register.blade.php` then you can set the flag as follows:
```php
'register_view'	=>	'auth.register',
```

#### `email_verify_view`
You may specify the view you might want to use for sending verification emails. The new value will overide the default view. If you want to load the default views for verification email then set the field to 'null'.

> **NOTE** A `verification_token` variable will be injected to the email verification view which will contain the user's verification token.

#### `session_identifier`
The session identifier will be helpful to retrieve the session's flash messages. Hence you may specify the session identifier according to your choice and may reference to it in the views.

###### Example:
Setting the session identifier:
```php
'session_identifier'	=> 'flash-message',
```

Flashing the data to the view:
```php
session()->flash(config('usersys.session_identifier'), 'Your flashed message!');
```

Retrieving the flashed message in the view:
```php
Session::get(config('usersys.session_identifier'));
```

> **NOTE** The function `config('usersys.session_identifier')` will return the value set to the `session_identifier` flag.

<a name="copyright"></a>
## Copyright and License

UserSys was written by Vijay Kumar and released under the MIT License.
See the [LICENSE](https://github.com/Qumonto/usersys/blob/master/LICENSE) file for details.

Copyright 2015 Vijay Kumar

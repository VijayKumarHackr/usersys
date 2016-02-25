<?php

namespace Qumonto\UserSys;

use Illuminate\Support\ServiceProvider;

class UserSysServiceProvider extends ServiceProvider {

	/**
	 * Register the bindings
	 * 
	 * @return void
	 **/
	public function register()
	{
		# code...
	}

	/**
	 * Post registration booting of services.
	 * 
	 * @return void
	 **/
	public function boot()
	{
		if(!$this->app->routesAreCached()) {
			require __DIR__.'/Http/routes.php';	
		}

		$this->loadViewsFrom(__DIR__.'/resources/views', 'usersys');

		$this->publishes([
				__DIR__.'/config/usersys.php' => config_path('usersys.php')
			], 'config');

		$this->publishes([
				__DIR__.'/migrations/' => database_path('migrations')
			], 'migrations');

		$this->publishes([
				__DIR__.'/resources/assets/css/' => public_path('css/'),
			], 'assets');

		$this->publishes([
				__DIR__.'/Events/UserRegistered.php'	=> base_path('app/Events/UserRegistered.php')
			], 'events');

		$this->publishes([
				__DIR__.'/Listeners/AccountVerificationEmail.php'	=> base_path('app/Listeners/AccountVerificationEmail.php')
			], 'listeners');

		$this->publishes([
				__DIR__.'/resources/lang/en/usersys.php'	=> base_path('app/resources/lang/en/usersys.php')
			], 'translations');

		$this->publishes([
				__DIR__.'/resources/views/password.blade.php'	=> base_path('app/resources/views/auth/password.blade.php'),
				__DIR__.'/resources/views/reset.blade.php'	=> base_path('app/resources/views/auth/reset.blade.php'),
			], 'password-views');

		$this->publishes([
				__DIR__.'/Http/Requests/'	=> base_path('app/Http/Requests/')
			], 'requests');
	}
}
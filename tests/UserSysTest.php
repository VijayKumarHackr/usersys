<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserSysTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Registration functionality test.
     *
     * @return void
     */
    public function testRegistration()
    {
        $this->expectsEvents(App\Events\UserRegistered::class);

        $this->visit('register')
             ->type('Bob@example.com', 'email')
             ->type('secret', 'password')
             ->type('secret', 'password_confirmation')
             ->press('Register')
             ->seePageIs('/login');
    }

    /**
     * Login and email verification functionality test.
     *
     * @return void
     */
    public function testLogin()
    {
        if(config('usersys.email_verification')){
            $this->visit('login')
                 ->type('Bob@example.com', 'email')
                 ->type('secret', 'password')
                 ->check('remember')
                 ->press('Login')
                 ->seePageIs(config('usersys.redirect_after_fail'));
        }
        else {
            $this->visit('login')
                 ->type('Bob@example.com', 'email')
                 ->type('secret', 'password')
                 ->check('remember')
                 ->press('Login')
                 ->seePageIs('/home');
        }      
    }

    public function testLogout()
    {
        $this->visit('logout')
             ->see(trans()->get('usersys.logout_message'));
    }
}

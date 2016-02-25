<?php

namespace Qumonto\UserSys;

use App\User;
use App\Events\UserRegistered;
use App\Http\Requests\Request;

trait AuthenticationTrait
{
    /**
     * Creates a new user.
     * 
     * @param array $data
     * @return App\User $user
     */
    public function createNewUser($data)
    {
        $verification_token = str_random(50);

        $user = User::create([
            $this->getUsernameColumnAndField() => $data[$this->getUsernameColumnAndField()],
            $this->getPasswordColumnAndField() => bcrypt($data[$this->getPasswordColumnAndField()]),
            'verification_token' => $verification_token,
            ]);

        \Event::fire(new UserRegistered($user));

        return $user;
    }

	 /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        return $request->only($this->getUsernameColumnAndField(), 'password');
    }

    /**
     * Get the username corresponding database column and field.
     * 
     * @return string
     */
    protected function getUsernameColumnAndField()
    {
        return config('usersys.username_column_and_field');
    }

    /**
     * Get the password corresponding database column and field.
     * 
     * @return string
     */
    protected function getPasswordColumnAndField()
    {
        return config('usersys.password_column_and_field');
    }

    /**
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage($spec)
    {
    	if ($spec == 'forCredentials') 
        {
        	return trans()->has('usersys.credentials')
                	? trans()->get('usersys.credentials')
                	: 'These credentials do not match our records.';
    	}
    	elseif ($spec == 'forActivation') 
        {
    		return trans()->has('usersys.inactive')
    				? trans()->get('usersys.inactive')
    				: 'Your email id is not verified.';
    	}
    }

    /**
     * Checking if the verification token exists.
     * 
     * @param string $token
     * @return boolean
     */
    protected function tokenExists($token)
    {
        return User::where('verification_token', $token)->first()->count() ? true : false;
    }
}
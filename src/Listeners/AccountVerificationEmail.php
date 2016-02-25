<?php

namespace App\Listeners;

use Mail;
use App\Events\UserRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class AccountVerificationEmail implements ShouldQueue
{
    protected $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $this->user = $event->user;

        if(config('usersys.email_verification'))
        {
            if(is_null(config('usersys.email_verify_view')))
            {
                $view = 'usersys::verify';
            }
            else
            {
                $view = 'usersys.email_verify_view';
            }

            Mail::send($view, 
                ['verification_token' => $this->user->verification_token], 
                function($message) 
                {
                    $message->to($this->user->email)
                            ->subject(trans('usersys.email_verification_subject'));
                });
        }
    }
}
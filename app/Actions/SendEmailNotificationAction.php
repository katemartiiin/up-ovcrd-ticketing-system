<?php

namespace App\Actions;

use Illuminate\Support\Facades\Mail;

use App\Mail\NotificationEmail;

class SendEmailNotificationAction {

    public function execute($data)
    {
        // The email sending is done using the to method on the Mail facade
        Mail::to($data['user']['email'])->send(new NotificationEmail($data));
    }

}
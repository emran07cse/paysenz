<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Sichikawa\LaravelSendgridDriver\SendGrid;

class InvoiceEmail extends Mailable
{
    use SendGrid, Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $addressFrom = env('MAIL_FROM_INVOICE');
        $addressReplyTo = env('MAIL_REPLYTO_INVOICE');
        $subject = 'Paysenz Invoice!';
        $name = 'Paysenz';

        $mail = $this->view('emails.invoice')
            ->from($addressFrom, $name)
            //->cc($addressCc, $name)
            //->bcc($addressFrom, $name)
            ->replyTo($addressReplyTo, $name)
            ->subject(!empty($this->data['subject']) ? $this->data['subject'] : $subject)
            ->with([ 'message' => $this->data['message'] ]);
        
        if(isset($this->data['attachment'])){
            $mail->attach($this->data['attachment']['path'], ['as' => $this->data['attachment']['display_name'], 'mime' => $this->data['attachment']['mime']]);
        }
        
        // Send Email    
        $mail->sendgrid([
                'personalizations' => [
                    [
                        'substitutions' => [
                            ':myname' => 's-ichikawa',
                        ],
                    ],
                ],
            ]);
    }
}
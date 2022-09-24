<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;
    public $filename;
    public $subject;
    public $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $filenames, $template)
    {
        //
        $this->filenames = $filenames;
        $this->subject = $subject;
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $buildMessage = $this->view($this->template)
            ->subject($this->subject);
        
        foreach ($this->filenames as $fileName){
            $buildMessage->attach($fileName);
        }
            
        return $buildMessage;
    }
}

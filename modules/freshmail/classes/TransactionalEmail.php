<?php

namespace FreshMail;

use FreshMail\Sender\Email;

class TransactionalEmail implements \JsonSerializable{

    private $recipient;
    private $from;
    private $subject;
    private $content;

    public function __construct(Email $recipient, Email $from, $subject, $content)
    {
        $this->recipient = $recipient;
        $this->from = $from;
        $this->subject = $subject;
        $this->content = $content;
    }

    public function jsonSerialize()
    {
        return  [
            'recipients' => [
                [
                    'email' => $this->recipient->email,
                    'name' => $this->recipient->name
                ]
            ],
            'from' => [
                'email' => $this->from->email,
                'name' => $this->from->name,
            ],
            'subject' => $this->subject,
            'contents' => [
                [
                    'type' => 'text/html',
                    'body' => $this->content
                ]
            ]
        ];
    }
}
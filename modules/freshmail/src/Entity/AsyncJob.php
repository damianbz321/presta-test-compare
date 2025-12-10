<?php

namespace FreshMail\Entity;

use ObjectModel;

class AsyncJob extends ObjectModel
{
    public $id_job;

    public $hash_list;

    public $parts;

    public $last_sync;

    public $job_status = 0;

    public $finished = 0;

    public $filename;

    public static $definition = [
        'table' => 'freshmail_async_job',
        'primary' => 'id_freshmail_async_job',
        'fields' => [
            'id_job' => [
                'type' => self::TYPE_INT,
                'required' => true
            ],
            'hash_list' => [
                'type' => self::TYPE_STRING,
                'required' => true
            ],
            'parts' => [
                'type' => self::TYPE_STRING,
                'required' => false
            ],
            'last_sync' => [
                'type' => self::TYPE_DATE,
                'required' => false
            ],
            'job_status' => [
                'type' => self::TYPE_BOOL,
                'required' => false
            ],
            'finished' => [
                'type' => self::TYPE_BOOL,
                'required' => false
            ],
            'filename' => [
                'type' => self::TYPE_STRING,
            ]
        ],
    ];


}
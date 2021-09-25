<?php

namespace RatchetChat\Domain;

class Channel
{
    public $channel_id;
    public $channel_name;
    public $datetime;
    public array $users;
    public array $messages;
}
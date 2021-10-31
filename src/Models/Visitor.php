<?php

namespace Models;

use Base\Model;

class Visitor extends Model
{
    protected string $table = 'visitors';

    public ?string $ip_address;
    public ?string $user_agent;
    public ?string $view_date;
    public ?string $page_url;
    public ?int $views_count;
}
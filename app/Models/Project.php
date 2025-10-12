<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Corcel\Model\Post as Corcel;

class Project extends Corcel
{
    protected $connection = 'wordpress';

    protected $postType = 'wps-project';
}

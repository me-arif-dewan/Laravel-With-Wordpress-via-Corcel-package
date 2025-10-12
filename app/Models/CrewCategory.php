<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Corcel\Model\Post as Corcel;

class CrewCategory extends Corcel
{
    protected $connection = 'wordpress';

    protected $postType = 'wps-crew-category';
}

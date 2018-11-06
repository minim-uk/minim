<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thesaurus extends Model
{


/*
TO IMPORT::

https://github.com/pmatseykanets/artisan-io/blob/master/readme.md

php artisan import:delimited thesaurus.csv "\App\Thes
s_HandS:7,Definition:8,Original_Language:9 -m insert 

 */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'thesaurusIDPrimary', 'Identifier', 'Level_0', 'Level_1', 'Level_2', 'Level_3', 'Synonyms','URIs','Same_as_HandS','Definition','Original_Language',
    ];



}

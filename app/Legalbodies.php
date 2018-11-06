<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Legalbodies extends Model
{
    protected $fillable = [
	    'legalBodyMDAcode',
	    'legalBodyName',
	    'legalBodyShortName',
	    'legalBodyDescription',
	    'legalBodyWebsite',
    ];


    /**
     * Legal Bodies (collections) can have many instruments
     *
     * @var array
     */
    public function instruments()
    {   

        return $this->hasMany('App\Instrument');

    }


}

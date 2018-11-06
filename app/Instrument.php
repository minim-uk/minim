<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    




    /**
     * An instrument is owned by a legal body
     *
     * @var array
     */
    public function legalbody()
    {   

        return $this->belongsTo('App\Legalbodies');

    }

}

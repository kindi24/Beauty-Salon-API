<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model{

    protected $fillable = ['name', 'duration'];

    public function specialists(){ return $this->belongsToMany(Specialist::class, 'service_specialist'); }
}

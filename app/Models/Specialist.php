<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model{

    protected $fillable = ['name'];

    public function services(){ return $this->belongsToMany(Service::class, 'service_specialist'); }
    public function appointments(){ return $this->hasMany(Appointment::class); }

}

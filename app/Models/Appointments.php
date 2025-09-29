<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointments extends Model{

    protected $fillable = ['specialist_id', 'service_id', 'date', 'start_time', 'end_time', 'client_name'];

    public function specialist() { return $this->belongsTo(Specialist::class); }
    public function service() { return $this->belongsTo(Service::class); }
}

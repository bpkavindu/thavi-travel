<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user() {
    return $this->belongsTo(User::class,'traveller_id');
}

public function guide() {
    return $this->belongsTo(User::class, 'guide_id');
}

public function tourPlan() {
    return $this->belongsTo(TourPlans::class);
}
}

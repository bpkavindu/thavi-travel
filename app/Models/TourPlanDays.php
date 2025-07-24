<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPlanDays extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function tourPlan()
{
    return $this->belongsTo(TourPlans::class);
}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPlans extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = ['id'];

public function days()
{
    return $this->hasMany(TourPlanDays::class, 'tour_plan_id');
}

public function images()
{
    return $this->hasMany(TourPlanImages::class, 'tour_plan_id');
}
}

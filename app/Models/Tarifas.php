<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifas extends Model
{
    use HasFactory;


    protected $fillable = [
        'plan_type',
        'subscription_fee',
        'yearly_fee',
        'halfyear_fee',
        'quarterly_fee',
        'monthly_fee',
        'anualmultiple_pays',
        'cant_person',
        'value_kid',
        'value_adult',
        'language',
        'yearly_subscription_discount',
        'halfyear_subscription_discount',
        'quarterly_subscription_discount',  
        'monthly_subscription_discount' 
    ];

}

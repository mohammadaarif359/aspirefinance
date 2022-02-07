<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmiPayment extends Model
{
    use HasFactory;
	protected $fillable = ['loan_id','pay_date','amount','created_at','updated_at'];
}

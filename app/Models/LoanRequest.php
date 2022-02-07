<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRequest extends Model
{
    use HasFactory;
	protected $fillable = ['user_id','amount','term','start_date','status','created_at','updated_at'];
}

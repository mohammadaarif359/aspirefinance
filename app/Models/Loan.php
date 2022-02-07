<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
	protected $fillable = ['loan_request_id','principle_amount','term','start_date','end_date','intrest','emi_amount_weekly','total_amount','loan_close','created_at','updated_at'];
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
			$table->bigInteger('loan_request_id');
			$table->double('principle_amount');
			$table->double('term');
			$table->date('start_date');
			$table->date('end_date');
            $table->double('intrest');
			$table->double('emi_amount_weekly');
			$table->double('total_amount');
			$table->tinyInteger('loan_close')->comment('0 is Running and 1 is Close');
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}

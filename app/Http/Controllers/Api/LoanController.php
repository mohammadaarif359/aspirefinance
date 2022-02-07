<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\EmiPayment;
use Validator;

class LoanController extends Controller
{
    public function emiPay(Request $request){
		$input = $request->all();
		$validator = Validator::make($request->all(),[
            'loan_id'=>'required',
			'pay_date'=>'required',
			'amount'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad request',
                'response' => [],
                'validation_error_responce' => $validator->errors(),
                'code' => 404,
            ]);
        }
		$loan = Loan::where('id',$input['loan_id'])->where('loan_close',0)->first();
		if(!empty($loan)) {
			$totalLoanAmount = $loan->total_amount;
			$totalPaid = EmiPayment::where('loan_id',$input['loan_id'])->sum('amount');
			$dueAmount =  $totalLoanAmount - $totalPaid;
			if($dueAmount >= $input['amount']) {
				$emiPayment = new EmiPayment();
				$emiPayment->loan_id = $loan->id;
				$emiPayment->pay_date = date('Y-m-d',strtotime($input['pay_date']));
				$emiPayment->amount = $input['amount'];
				$emiPayment->save();
				
				// cloase the loan if total loan amount is cover
				$totalPaidTill = $totalPaid +  $input['amount'];
				if($totalPaidTill == $totalLoanAmount) {
					// close the loan
					$loan->loan_close = 1;
					$loan->save();
					$msg = 'Emi payment sucessfully and you loan account is closed';
				} else {
					$msg = 'Emi payment sucessfully';
				}
				return response()->json([
					'status' => $msg,
					'response' => [],
					'code' => 200,
				]);
			} else {
				return response()->json([
					'status' => 'Emi amount should not greater then due amount',
					'response' => [],
					'code' => 204,
				]);
			}
		} else {
			return response()->json([
				'status' => 'Currently you not have running loan account according given information',
				'response' => [],
				'code' => 202,
			]);
		}
	}
}

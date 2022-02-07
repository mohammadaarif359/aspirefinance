<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoanRequest;
use App\Models\Loan;
use Validator;
use Auth;

class LoanRequestController extends Controller
{
	public function requestCreate(Request $request) {
		$input = $request->all();
		$validator = Validator::make($request->all(),[
            'amount'=>'required|numeric|min:10000',
			'term'=>'required|numeric|min:1|max:20',
			'start_date'=>'required|after_or_equal:today',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad request',
                'response' => [],
                'validation_error_responce' => $validator->errors(),
                'code' => 404,
            ]);
        }
		$loanRequest = new LoanRequest();
		$loanRequest->user_id = Auth::user()->id;
		$loanRequest->amount = $input['amount']; 
		$loanRequest->term = $input['term'];
		$loanRequest->start_date = date('Y-m-d',strtotime($input['start_date']));
		$loanRequest->status = 0;
		$loanRequest->save();
		if($loanRequest){
			return response()->json([
				"status"=>"loan apply successfully",
				"code"=>200,
				"response"=>[]
			]);
		} else {
			return response()->json([
				"status"=>"something went wrong",
				"code"=>500,
				"response"=>[]
			]);
		}
	}
	public function requestApprove(Request $request) {
		$input = $request->all();
		$configStatus = config('aspire.loan_request_status');
		$validator = Validator::make($request->all(),[
            'loan_request_id'=>'required|numeric',
			'status'=>'required|numeric|in:1,2'
        ],[
			'loan_request_id.required'=>'Loan request id field is required',
			'status.required'=>'Stauts field is required',
			'status.in'=>'Status muest be 1 or 2 which means Approved or Rejected'
		]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad request',
                'response' => [],
                'validation_error_responce' => $validator->errors(),
                'code' => 404,
            ]);
        }
		$data = LoanRequest::where('id',$input['loan_request_id'])->first();
		if($data) {
			if(Loan::where('loan_request_id',$input['loan_request_id'])->exists()) {
				return response()->json([
					"status"=>"loan request id is already approved",
					"code"=>406,
					"response"=>[]
				]);
			}
			
			$data->status = $input['status'];
			$data->save();
			$response = [];
			if($input['status'] == 1) {
				
				// loan end date calculation
				$start_date = $data->start_date;
				$end_date = date('Y-m-d', strtotime('+'.$data->term. 'year', strtotime($start_date)));
				
				// intrest calucalation
				$rate = 10;
				$intrest = ($data->amount * $data->term  * $rate) / 100;
				
				// final amount with intrest
				$total_amount = $data->amount + $intrest;
				
				// weekly emi calculation
				$dayDiff = strtotime($end_date, 0) - strtotime($start_date, 0);
				$weeks = floor($dayDiff / 604800);
				$emi_amount_weekly = $total_amount / $weeks;
				
				//echo 'end date',$end_date;
				//echo 'weeks',$weeks;
				//echo 'emi_amount_weekly',$emi_amount_weekly;
		
				$loan = new Loan();
				$loan->loan_request_id = $data->id;
				$loan->principle_amount = $data->amount; 
				$loan->term = $data->term;
				$loan->start_date = $start_date;
				$loan->end_date = $end_date;
				$loan->intrest = $intrest;
				$loan->emi_amount_weekly = number_format($emi_amount_weekly,2);
				$loan->total_amount = $total_amount;
				$loan->save();
				
				$response['principle_amount'] = $data->amount;
				$response['term'] = $data->term;
				$response['emi_amount_weekly'] = number_format($emi_amount_weekly,2);
				$response['total_amount'] = $total_amount;
			}
			return response()->json([
				"status"=>"loan request status updated to ". $configStatus[$input['status']] ." successfully",
				"code"=>200,
				"response"=>$response
			]);
		} else {
			return response()->json([
				"status"=>"loan request id not found",
				"code"=>202,
				"response"=>[]
			]);
		}
	}
}

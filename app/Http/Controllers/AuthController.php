<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\Token;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;

use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function balance(Request $request) {

        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $user = User::where(['document' => $request->input('document'), 'phone' => $request->input('phone')])->first();
    	if (!$user) {
    		return response()->json(array('error' => 'Unable to find a user matching that email address and document.'), 401);
    	}

        $balance = Transaction::getBalance($user->id);

        return response()->json(['user' => $user, 'balance'=> $balance], 200, []);

    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'document' => 'required|unique:users',
            'phone' => 'required',
            'email' => 'required|email|unique:users'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        if (User::where('email', '=', $request->input('email'))->first()) {
            return response()->json(array('error' => 'An account already exists with this email address.'), 400);      
        }

        $user = new User();
        $user->fill($request->all());    
        $user->save();
        
        $wallet = new Wallet();
        $wallet->fill(['user_id' => $user->id]);   
        $wallet->save();

        $transaction = new Transaction();
        $transaction->fill(['user_id' => $user->id, 'amount' => 0, 'type'=>'add']);   
        $transaction->save();

        $token = Token::generateForUser($user->id);

        return $this->balance($request);
    }

    public function getAccount() {

        $user = User::where('id', $this->currentUser()->id)->first();

        if(!$user) {
            return response()->json(['error' => 'User profile not found'], 400, []);
        }

        return response()->json($user, 200, []);
    }

    public function updateAccount(Request $request) {

        $data = $request->all();

        $user = User::find($this->currentUser()->id);

        if(!$user) {
            return response()->json(['error' => 'User profile not found'], 400, []);
        }

        $user->fill($data);
        $user->save();

        return response()->json($user, 200, []);
    }

}
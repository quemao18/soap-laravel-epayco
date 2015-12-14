<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\Token;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request) {

        $email = $request->input('email');
        $password = $request->input('password');
        if (!$email || !$password) {
        	return response()->json(array('error' => 'You must provide an email address and password.'), 400);
        }

        $hashedPassword = User::hashedPassword($password);
        $user = User::where(['password' => $hashedPassword, 'email' => $email])->first();
    	if (!$user) {
    		return response()->json(array('error' => 'Unable to find a user matching that email address and password.'), 401);
    	}

        $token = Token::generateForUser($user->id);

        return response()->json(['user' => $user, 'token' => $token->token], 200, []);

    }

    public function register(Request $request) {

        $fields = array('email', 'password');
        foreach ($fields as $field) {
            if (!$request->input($field)) {
                return response()->json(array('error' => 'Missing field: '.$field), 400);
            }
        }

        if (User::where('email', '=', $request->input('email'))->first()) {
            return response()->json(array('error' => 'An account already exists with this email address.'), 400);      
        }

        $user = new User();
        $user->fill($request->all());
        $user->password = User::hashedPassword($request->input('password'));        
        $user->save();

        return $this->login($request);
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
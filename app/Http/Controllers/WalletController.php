<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
// require 'vendor/autoload.php';

use App\Models\Token;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;
use App\Mail\UserToken;


use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{

    public function addMoney(Request $request) {

        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'phone' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(array('error' => $validator->errors()), 400);      
        }

        $amount = $request->input('amount');
        
        $user = User::where(['document' => $request->input('document'), 'phone' => $request->input('phone')])->first();
    	if (!$user) {
    		return response()->json(array('error' => 'Unable to find a user matching that phobe and document.'), 401);
        }
        
        $walletIdAdd = Wallet::where('user_id', $user->id)->first()->id;
        $transaction = Transaction::generateForUser($walletIdAdd, $amount, 'add', 1, '');
        $balance = Transaction::getBalance($user->id);

        return response()->json(['user' => $user, 'balance' => $balance], 200, []);
    }

    public function sendMoney(Request $request) {

        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'phone' => 'required',
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(array('error' => $validator->errors()), 400);      
        }

        $amount = $request->input('amount');
        
        $user = User::where(['document' => $request->input('document'), 'phone' => $request->input('phone')])->first();
    	if (!$user) {
    		return response()->json(array('error' => 'Unable to find a user matching that phone and document.'), 401);
        }

        $userSending = User::where('id', $this->currentUser()->id)->first();

        $walletIdSend = Wallet::where('user_id', $userSending->id)->first()->id;
        $walletIdAdd = Wallet::where('user_id', $user->id)->first()->id;

        $token = Token::generateForUserSend($user->id);
        $balance = Transaction::getBalance($userSending->id);

        if ($balance<=0) {
    		return response()->json(array('error' => "You don't have a balance to send"), 401);
        }
        if ($walletIdSend === $walletIdAdd) {
    		return response()->json(array('error' => "You can't sent your self"), 401);
        }

        $mail = $this->sendEmail($userSending, $token);

        if($mail){
            $transactionSend = Transaction::generateForUser($walletIdSend, $amount, 'send', 0, $token);
            $transactionAdd = Transaction::generateForUser($walletIdAdd, $amount, 'add', 0, $token);
            $message = 'Check your email';
            return response()->json([
                'userReceive' => $user, 
                'token' => $token, 
                'balance' => $balance, 
                'transactionSend' => $transactionSend,
                'transactionAdd'=> $transactionAdd,
                'mail' => $message
            ], 200, []);
        }
        


    }

    public function updateTransaction(Request $request) {

        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(array('error' => $validator->errors()), 400);      
        }

        $data = $request->all();

        $transaction =  Transaction::where('token', '=', $data['token']);

        if(!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 400, []);
        }
        
        $transaction->update(['status' => 1]);

    	return response()->json(array('success' => "Transaction update success"), 200);

    }

    protected function sendEmail($user, $token)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;              // Enable verbose debug output
            $mail->isSMTP();                                       // Send using SMTP
            $mail->Host       = env('MAIL_HOST');                  // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                              // Enable SMTP authentication
            $mail->Username   = env('MAIL_USERNAME');              // SMTP username
            $mail->Password   = env('MAIL_PASSWORD');              // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;       // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = env('MAIL_PORT');                  // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), 'Epayco');
            $mail->addAddress($user->email, $user->name);     // Add a recipient
     
            // Content
            $mail->isHTML(true);                              // Set email format to HTML
            $mail->Subject = 'Token for send money';
            $mail->Body    = 'Your token is <b>'.$token.'</b>';
            $mail->AltBody = 'Your token is '.$token;

            return $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            response()->json(['error' => $mail->ErrorInfo], 400, []);
        }
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Transaction extends Model  
{
	protected $fillable = [
		'wallet_id',
		'amount',
		'type',
		'status',
		'token'
	];

	
	public static function generateForUser($wallet_id, $amount, $type, $status, $token) {
		$trans = new Transaction;
		$trans->{'wallet_id'} = $wallet_id;
		$trans->{'type'} = $type;
		$trans->{'amount'} = $amount;
		$trans->{'status'} = $status;
        $trans->{'token'} = $token;

        $trans->save();
        return $trans;
	}

	public static function getBalance($user_id){
		$add = Transaction::join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
		->where('transactions.type', '=', 'add')->where('transactions.status', '=', 1)
		->where('user_id', '=', $user_id)
		->sum('transactions.amount');

		$send = Transaction::join('wallets', 'transactions.wallet_id', '=', 'wallets.id')
		->where('transactions.type', '=', 'send')->where('transactions.status', '=', 1)
		->where('user_id', '=', $user_id)
		->sum('transactions.amount');
		
		return $add - $send;
	}
}
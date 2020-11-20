<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Request;

final class Token extends Model  
{
	public $timestamps = false;

	public function updateExpiration() {
		$this->expires = Token::expirationTime();
		$this->save();
	}
	
	/* STATIC METHODS */

	public static function generateForUser($user_id) {
		Token::where('user_id', '=', $user_id)->delete();

		$token = new Token;
        $token->{'user_id'} = $user_id;
        $token->token = $user_id.'-'.Token::v4UUID();
        $token->expires = Token::expirationTime();
        $token->save();
        return $token;
	}

	public static function generateForUserSend($user_id) {
        return Token::v4UUID_6();
	}

	public static function expirationTime() {
		return time() + (60 * 20); // 20 minutes time
	}

	public static function v4UUID() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand(0, 0xffff), mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	public static function v4UUID_6() {
		return sprintf('%06x',
		mt_rand(0, 0xffffff),
		);
	}
}
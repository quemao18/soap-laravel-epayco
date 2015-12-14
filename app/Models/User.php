<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class User extends Model  
{

	protected $hidden = ['password'];

	protected $fillable = [
		'email',
		'telephone',
		'name',
		'address_1',
		'address_2',
		'county',
		'city',
		'postcode'
	];

	public static function hashedPassword($password) {
		$salt = '429ae64f-4d0f-49c0-a79a-68c9c75f550d';
		return hash('sha256', $salt.$password);
	}

}
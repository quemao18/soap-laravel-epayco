<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class Wallet extends Model  
{
	protected $fillable = [
		'user_id',
	];
}
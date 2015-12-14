<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;

class Controller extends BaseController
{
    public function currentUser() {
		$user_id = session()->get('user_id');
		return User::find($user_id);
	}
}

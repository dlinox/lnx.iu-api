<?php

namespace App\Modules\Auth\Support;

use App\Modules\Role\Models\Role;
use App\Modules\User\Models\User;

class AuthSupport
{

  public static function generateCodeVerification()
  {
    $code = rand(100000, 999999);
    return ['plain' => $code, 'encrypted' => encrypt($code)];
  }

  public static function generatePassword()
  {
    return rand(10000000, 99999999);
  }
  
}

<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;

class UserRepository
{
    public static function findByUsernameAndType($username, $modelType)
    {
        return User::select('users.*',)
            ->where(function ($query) use ($username) {
                $query->where('username', $username)
                    ->orWhere('email', $username);
            })
            ->where('users.model_type', $modelType)
            ->first();
    }

    public static function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function updatePassword(User $user, $password)
    {
        $user->password = $password;
        $user->save();
    }
}

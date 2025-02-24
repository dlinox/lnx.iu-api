<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Auth\Http\Requests\SignUpRequest;
use App\Modules\Person\Models\Person;
use App\Modules\Student\Models\Student;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function signIn(Request $request)
    {
        $user = $this->user->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('email', $request->username)
            ->orWhere('username', $request->username)
            ->where('roles.name', 'admin')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('', 'Credenciales incorrectas');
        }

        if ($user->is_enabled == 0) {
            return ApiResponse::error('', 'Usuario inactivo',);
        }

        return ApiResponse::success($this->userState($user));
    }

    public function signInStudent(Request $request)
    {
        $user = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            // ->where('email', $request->username)
            // ->orWhere('username', $request->username)
            ->where(function ($query) use ($request) {
                $query->where('email', $request->username)
                    ->orWhere('username', $request->username);
            })
            ->where('roles.name', 'estudiante')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('', 'Credenciales incorrectas');
        }

        if ($user->is_enabled == 0) {
            return ApiResponse::error('', 'Usuario inactivo',);
        }

        return ApiResponse::success($this->userState($user));
    }

    //signUp
    public function signUp(SignUpRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();

            $person = Person::registerItem($data);
            $data['person_id'] = $person->id;

            Student::registerItem($data);

            $user = User::create([
                'name' => $person->name . ' ' . $person->last_name_father . ' ' . $person->last_name_mother,
                'username' => $person->document_number,
                'email' => $data['email'],
                'password' => Hash::make($person->document_number),
                'is_enabled' => 1,
            ]);

            $user->assignRole('estudiante');
            DB::commit();
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }


    public function signOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success('Sesión cerrada, hasta luego');
    }

    public function user(Request $request)
    {
        $user =  $request->user();
        $userState = $this->userState($user);
        return ApiResponse::success($userState);
    }

    private function userState($user)
    {
        $role = $this->getUserRole($user);
        $currentUser = User::find($user->id);

        return [
            'token' => $this->getUserToken($currentUser),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role->name,
                'redirectTo' => "/",
            ],
            'permissions' => implode('|', $user->getAllPermissions()->pluck('name')->toArray()),
        ];
    }

    private function getUserToken($user)
    {

        $token = request()->bearerToken();

        if ($token) {
            return $token;
        }

        // Si no hay token en el header, crear uno nuevo
        return $user->createToken($user->email)->plainTextToken;
    }

    private function getUserRole($user)
    {
        try {

            $role = Role::where('name', $user->getRoleNames()[0])->first();
            if (!$role) {
                return ApiResponse::error('El usuario no tiene un rol asignado', 401);
            }
            return $role;
        } catch (\Exception $e) {
            throw new \Exception('Error al obtener el rol del usuario');
        }
    }
}

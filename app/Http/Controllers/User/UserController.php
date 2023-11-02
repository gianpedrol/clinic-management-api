<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\SendLoginUser;
use App\Models\Procedimento;
use App\Models\ProfissionalAgenda;
use App\Models\ProfissionalProcedimento;
use App\Models\ProfissionalServico;
use App\Models\User;
use Illuminate\Http\Request;
use Mail;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');

        if (!auth()->user()) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }
    }

    public function listUsers()
    {
        $users = User::all();

        return response()->json(['status' => 'ok', 'message' => 'list users', $users], 200);
    }

    public function showUser($id)
    {
        $user = User::where('id', $id)->first();

        if (empty($user)) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json(['status' => 'ok', 'message' => 'show user', $user], 200);
    }

    public function createUser(Request $request)
    {

        /** Role ID 2 -> Proffisional
         *  Role ID 3 -> Cliente
         *
         */

        $data = $request->only('name', 'email', 'cpf', 'birthdate', 'phone', 'role_id', 'password', 'procedimentos');
        $user = User::where(['email' => $data['email']])->first();
        if ($user) {
            return response()->json(['error' => 'User Already exists'], 400);
        }

        $senha_md5 = '654321'; //Descomentar após testes
        //$senha_md5 = $data['password'];
        $senha_temp = bcrypt($senha_md5);

        try {
            \DB::beginTransaction();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $senha_temp,
                'cpf' => $data['cpf'],
                'birthdate' => $data['birthdate'],
                'phone' => $data['phone'],
                'role_id' => $data['role_id']
            ]);

            if ($data['role_id'] == 2) {
                foreach ($data['procedimentos'] as $item)
                    $procedimento = Procedimento::findOrfail($item->procedimento_id);
                $user['procedimentos'] = ProfissionalProcedimento::create([
                    'user_id' => $user->id,
                    'procedimento_id' =>  $procedimento->id,
                    'price' => $item->price,
                ]);
            }
            \DB::commit();

            $item = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $senha_md5
            ];


            Mail::to([$data['email']])->send(new SendLoginUser($item));
        } catch (\Throwable $th) {
            \DB::rollback();

            return response()->json([$th->getMessage()], 500);
        }

        return response()->json(['status' => 'ok',  'message' => 'user created', $user], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        try {
            \DB::beginTransaction();

            $user->delete();

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();

            return response()->json([$th->getMessage()], 500);
        }

        return response()->json(['status' => 'ok', 'message' => 'User deleted successfully'], 200);
    }

    public function listUsuariosProcedimentos($id)
    {
        $procedimento = Procedimento::findOrfail($id);

        $procedimentosUsers = ProfissionalProcedimento::where('procedimento_id', $procedimento->id)->get();

        $users = [];
        foreach ($procedimentosUsers as $item) {
            $users[] = User::where("id", $item->user_id)->first();
        }

        return response()->json(['status' => 'ok', 'message' => 'Lista de Uusários profissional', $users], 200);
    }

    public function listarHorariosDisponiveis($profissionalId)
    {
        $horariosDisponiveis = ProfissionalAgenda::where('profissional_id', $profissionalId)
            ->where('disponivel', true)
            ->get();

        return response()->json(['status' => 'ok', 'message' => 'Lista de Horários disponiveis', $horariosDisponiveis], 200);
    }
}

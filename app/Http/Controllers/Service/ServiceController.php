<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Controller;
use App\Models\Servico;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

        if (!auth()->user()) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }
    }

    public function createService(Request $request){
        $data = $request->only('servico', 'percentual');

        try {
            \DB::beginTransaction();

            $servico = Servico::create([
                'servico' => $data['servico'],
                'percentual' => $data['percentual']
            ]);
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();

            throw $th;
        }

        return response()->json(['status' => 'ok', 'message' => 'servico criado', $servico], 200);
    }

    public function listServicos(){
        $servicos = Servico::all();

        return response()->json(['status' => 'ok', 'message' => 'Lista de Servicos', $servicos], 200);
    }

    public function showServico($id){
        $servico = Servico::findOrFail($id);

        return response()->json(['status' => 'ok', 'message' => 'Servico detalhe', $servico], 200);
    }


    public function updateServico($id, Request $request){

        $data = $request->only('servico', 'percentual');
        $servico = Servico::findOrFail($id);

        try {
            \DB::beginTransaction();

            $servico = Servico::where('id', $servico->id)->update([
                'servico' => $data['servico'],
                'percentual' => $data['percentual']
            ]);
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollback();

            throw $th;

        }


        return response()->json(['status' => 'ok', 'message' => ' Atualização Servico Done! ', $servico], 200);
    }


}

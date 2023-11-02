<?php

namespace App\Http\Controllers\Pacote;

use App\Http\Controllers\Controller;
use App\Models\Pacote;
use App\Models\Procedimento;
use App\Models\Servico;
use Illuminate\Http\Request;

class PacoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

        if (!auth()->user()) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }
    }

    public function createService(Request $request)
    {
        $data = $request->only('servico', 'percentual');

        try {
            \DB::beginTransaction();

            $servico = Pacote::create([
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

    public function listPacotes()
    {
        $pacotes = Pacote::all();

        foreach ($pacotes as $item) {
            $item['procedimentos'] = Procedimento::where('pacote_id', $item->id)->get();
        }

        return response()->json(['status' => 'ok', 'message' => 'Lista de Pacotes', $pacotes], 200);
    }

    public function showPacote($id)
    {
        $pacote = Pacote::findOrFail($id);

        $pacote['procedimentos'] = Procedimento::where('pacote_id', $pacote->id)->get();



        return response()->json(['status' => 'ok', 'message' => 'Pacote detalhe', $pacote], 200);
    }

    public function showProcedimento($id)
    {
        $procedimento = Procedimento::findOrfail($id);

        return response()->json(['status' => 'ok', 'message' => 'Pacote detalhe', $procedimento], 200);
    }


    public function updateServico($id, Request $request)
    {

        $data = $request->only('servico', 'percentual');
        $servico = Pacote::findOrFail($id);

        try {
            \DB::beginTransaction();

            $servico = Pacote::where('id', $servico->id)->update([
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

    public function listaProcedimentos()
    {
        $procedimentos = Procedimento::all();

        return response()->json(['status' => 'ok', 'message' => ' Lista de procedimentos  ', $procedimentos], 200);
    }
}

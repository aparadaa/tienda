<?php

namespace App\Http\Controllers\Inventarios;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use Illuminate\Http\Request;

class InventariosController extends Controller
{
    public function index(Request $request)
    {
        $misBodegas = auth()->user()->bodegas
            ->map(function ($bodega) {
                return ['id' => $bodega->id, 'nombre' => $bodega->nombre];
            })
            ->toArray();

        $params = [
            'mis_bodegas' => $misBodegas,
        ];

        return view('component')
            ->with('params', $params)
            ->with('component', 'inventarios-inventarios');
    }

    public function show($id)
    {
        $lotes = Lote::where('bodega_id', $id)
            ->where('existencia', '>', 0)
            ->with('producto')
            ->orderBy('expiracion')
            ->get();

        $params = [
            'lotes' => $lotes,
        ];

        return response()->json($params);
    }
}

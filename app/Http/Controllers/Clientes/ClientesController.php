<?php

namespace App\Http\Controllers\Clientes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\ClienteStoreRequest;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClientesController extends Controller
{

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(ClienteStoreRequest $request)
    {
        $cliente            = new Cliente;
        $cliente->id        = $request->id;
        $cliente->email     = $request->email;
        $cliente->nombres   = $request->nombres;
        $cliente->direccion = $request->direccion;
        $cliente->save();

        return response()->json(Cliente::find($request->id));
    }

    public function show(Request $request, $id)
    {
        $cliente = Cliente::find($id);
        return response()->json($cliente);
    }

    public function edit(Cliente $cliente)
    {
        //
    }

    public function update(Request $request, Cliente $cliente)
    {
        //
    }

    public function destroy(Cliente $cliente)
    {
        //
    }
}

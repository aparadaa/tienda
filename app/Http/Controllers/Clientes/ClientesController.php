<?php
namespace App\Http\Controllers\Clientes;

use App\Models\Cliente;
use Csgt\Crud\CrudController;
use Illuminate\Http\Request;

//use App\Models\Model;

class ClientesController extends CrudController
{
    public function __construct()
    {
        $this->setModel(new Cliente);
        $this->setTitle('Clientes');

        $this->setBreadcrumb([
            ['url' => '', 'title' => 'Catálogos', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Clientes', 'icon' => 'fa fa-university'],
        ]);

        $this->setField(['name' => 'NIT', 'field' => 'id', 'editable' => true]);
        $this->setField(['name' => 'Email', 'field' => 'email']);
        $this->setField(['name' => 'Nombres', 'field' => 'nombres']);
        $this->setField(['name' => 'Direccion', 'field' => 'direccion']);

        $this->middleware(function ($request, $next) {
            return $next($request);
        });

        $this->setPermissions("\Cancerbero::crudPermissions", 'clientes.clientes');
    }

    public function store(Request $request)
    {
        if ($request->expectsJson()) {
            $rules = [
                'nombres'   => 'required',
                'direccion' => 'required',
            ];

            $request->validate($rules);

            $cliente            = new Cliente;
            $cliente->id        = $request->id;
            $cliente->email     = $request->email;
            $cliente->nombres   = $request->nombres;
            $cliente->direccion = $request->direccion;
            $cliente->save();

            return response()->json($cliente);
        }

        return parent::store($request);
    }

    public function show(Request $request, $id)
    {
        if ($request->expectsJson()) {
            $cliente = Cliente::find($id);
            return response()->json($cliente);
        }

        return parent::show($request, $id);
    }
}

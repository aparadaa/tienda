<?php
namespace App\Http\Controllers\Bodegas;

use App\Models\Auth\User;
use App\Models\Bodega;
use App\Models\UserBodega;
use Csgt\Crud\CrudController;
use Illuminate\Http\Request;

//use App\Models\Model;

class UserBodegasController extends CrudController
{
    public function __construct()
    {
        $this->setModel(new UserBodega);
        $this->setJoin('usuarios');
        $this->setJoin('bodegas');

        $this->setTitle('Bodegas de usuarios');
        $this->setBreadcrumb([
            ['url' => '', 'title' => 'Bodegas', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Bodegas de usuarios', 'icon' => 'fa fa-university'],
        ]);

        $this->setField(['name' => 'Usuario', 'field' => 'usuarios.name']);
        $this->setField(['name' => 'Bodegas', 'field' => 'bodegas.nombre']);

        $this->middleware(function ($request, $next) {
            $this->setField([
                'name'       => 'Usuario',
                'field'      => 'user_id',
                'type'       => 'combobox',
                'collection' => User::select('id', 'name')->get(),
            ]);

            $this->setField([
                'name'       => 'Bodega',
                'field'      => 'bodega_id',
                'type'       => 'combobox',
                'collection' => Bodega::select('id', 'nombre')->get(),
            ]);
            return $next($request);
        });

        $this->setPermissions("\Cancerbero::crudPermissions", 'bodegas.bodegas-usuarios');
    }

    public function store(Request $request)
    {
        $userBodega = UserBodega::where([
            'user_id'   => $request->user_id,
            'bodega_id' => $request->bodega_id,
        ])->first();

        if ($userBodega) {
            return redirect('/bodegas/bodegas-usuarios');
        }

        return parent::store($request);
    }
}

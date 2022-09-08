<?php
namespace App\Http\Controllers\Bodegas;

use App\Models\Bodega;
use Csgt\Crud\CrudController;

//use App\Models\Model;

class BodegasController extends CrudController
{
    public function __construct()
    {
        $this->setModel(new Bodega);
        $this->setTitle('Bodegas');
        $this->setBreadcrumb([
            ['url' => '', 'title' => 'Bodegas', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Bodegas', 'icon' => 'fa fa-university'],
        ]);
        $this->setField(['name' => 'Nombre', 'field' => 'nombre']);
        $this->setField(['name' => 'Descripción', 'field' => 'descripcion']);
        $this->setField(['name' => 'Direccion', 'field' => 'direccion']);

        $this->middleware(function ($request, $next) {
            return $next($request);
        });

        $this->setPermissions("\Cancerbero::crudPermissions", 'bodegas.bodegas');
    }
}

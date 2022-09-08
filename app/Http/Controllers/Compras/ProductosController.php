<?php
namespace App\Http\Controllers\Compras;

use App\Models\Producto;
use Csgt\Crud\CrudController;

class ProductosController extends CrudController
{
    public function __construct()
    {
        $this->setModel(new Producto);
        $this->setTitle('Productos');
        $this->setBreadcrumb([
            ['url' => '', 'title' => 'Compras', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Productos', 'icon' => 'fa fa-user'],
        ]);

        $this->setField(['name' => 'Nombre', 'field' => 'nombre']);
        $this->setField(['name' => 'Marca', 'field' => 'marca']);
        $this->setField(['name' => 'Descripción', 'field' => 'descripcion']);
        $this->setField(['name' => 'Codigo de barra', 'field' => 'codigo_barra']);

        $this->middleware(function ($request, $next) {
            return $next($request);
        });

        $this->setPermissions("\Cancerbero::crudPermissions", 'compras.productos');
    }
}

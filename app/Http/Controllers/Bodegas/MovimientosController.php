<?php

namespace App\Http\Controllers\Bodegas;

use App\Models\Bodega;
use App\Models\EstadoMovimiento;
use App\Models\Lote;
use App\Models\Movimiento;
use App\Models\Producto;
use App\Models\TipoMovimiento;
use App\Models\TipoPago;
use Csgt\Crud\CrudController;
use Illuminate\Http\Request;

class MovimientosController extends CrudController
{

    public function __construct()
    {
        $this->setModel(new Movimiento);
        $this->setJoin('bodega');
        $this->setJoin('tipo');
        $this->setJoin('estado');

        $this->setTitle('Movimientos de bodegas');

        $this->setBreadcrumb([
            ['url' => '', 'title' => 'Bodegas', 'icon' => 'fa fa-book'],
            ['url' => '', 'title' => 'Bodegas de usuarios', 'icon' => 'fa fa-university'],
        ]);

        $this->setField(['name' => 'ID', 'field' => 'id']);
        $this->setField(['name' => 'Bodega', 'field' => 'bodega.nombre']);
        $this->setField(['name' => 'Movimiento', 'field' => 'tipo.nombre']);
        $this->setField(['name' => 'Estado', 'field' => 'estado.descripcion']);

        $this->middleware(function ($request, $next) {
            $bodegas = auth()->user()->bodegas->pluck('id')->toArray();

            if (count($bodegas) == 0) {
                abort(403, 'Actualmente no tienes una bodega asociada.');
            }

            $this->setWhereIn('bodega_id', $bodegas);
            // $this->setOrderBy(['column' => 'id', 'direction' => 'desc']);

            return $next($request);
        });

        $this->setPermissions("\Cancerbero::crudPermissions", 'bodegas.movimientos');
    }

    public function create(Request $request)
    {
        return $this->edit($request, 0);
    }

    public function edit(Request $request, $id)
    {
        $misBodegas = auth()->user()->bodegas
            ->map(function ($bodega) {
                return ['id' => $bodega->id, 'nombre' => $bodega->nombre];
            })
            ->toArray();

        $bodegas = Bodega::query()
            ->select('id', 'nombre')
            ->where('activo', true)
            ->get();
        $estados         = EstadoMovimiento::select('id', 'descripcion')->get();
        $tipoPagos       = TipoPago::select('id', 'descripcion')->get();
        $tipoMovimientos = TipoMovimiento::select('id', 'nombre')->get();

        $params = [
            'id'               => $id,
            'mis_bodegas'      => $misBodegas,
            'estados'          => $estados,
            'tipo_pagos'       => $tipoPagos,
            'tipo_movimientos' => $tipoMovimientos,
            'bodegas'          => $bodegas,
        ];

        return view('component')
            ->with('params', $params)
            ->with('component', 'bodegas-movimientos');
    }

    public function detail(Request $request, $accion)
    {
        switch ($accion) {
            case 'data_movimiento':
                $movimiento = self::dataMovimiento($request->id);
                return response()->json($movimiento);
            case 'data_producto':
                $producto = self::dataProducto($request->codigo_barra, $request->bodega_id);
                return response()->json($producto);
        }
    }

    public function dataProducto($codigoBarras, $bodegaId)
    {
        $producto = Producto::query()
            ->where('codigo_barra', $codigoBarras)
            ->first();

        if (is_null($producto)) {
            abort(404, 'No se encontró el producto con el código de barras: ' . $codigoBarras);
        }

        $existencia = Lote::query()
            ->where('bodega_id', $bodegaId)
            ->where('producto_id', $producto->id)
            ->sum('existencia');

        if ($existencia <= 0) {
            abort(404, 'No hay existencia del producto con el código de barras: ' . $codigoBarras);
        }

        $producto->{'existencia'} = $existencia;
        return $producto;
    }

    public static function dataMovimiento($id)
    {
        if ($id == 0) {
            $misBodegas = auth()->user()
                ->bodegas
                ->map(function ($bodega) {
                    return ['id' => $bodega->id, 'nombre' => $bodega->nombre];
                });

            return [
                'id'                   => 0,
                'bodega_id'            => 0,
                'tipo_movimiento_id'   => 0,
                'estado_movimiento_id' => 0,
                'tipo_pago_id'         => 0,
                'recibe_bodega_id'     => 0,
                'observaciones'        => '',
                'detalles'             => [],
                'tipo'                 => [
                    'id'     => 2,
                    'nombre' => 'Venta',
                    'signo'  => '-',
                ],
                'bodega'               => $misBodegas->first(),
                'estado'               => [
                    'id'          => 0,
                    'descripcion' => '',
                ],
                'tipoPago'             => [
                    'id'          => 1,
                    'descripcion' => 'Efectivo',
                ],
                'bodegaRecibe'         => [
                    'id'     => 0,
                    'nombre' => '',
                ],
                'cliente'              => [
                    'id'        => 0,
                    'nombres'   => '',
                    'direccion' => '',
                ],
            ];
        }

        $movimiento = Movimiento::query()
            ->with('bodega')
            ->with('tipo')
            ->with('estado')
            ->with('tipoPago')
            ->with('detalles')
            ->with('bodegaRecibe')
            ->with('cliente')
            ->findOrFail($id);

        return $movimiento;
    }
    // 'store', 'update', 'data', 'detail', 'show'

}

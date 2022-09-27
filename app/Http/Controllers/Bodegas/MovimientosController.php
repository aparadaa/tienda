<?php

namespace App\Http\Controllers\Bodegas;

use App\Models\Bodega;
use App\Models\EstadoMovimiento;
use App\Models\Lote;
use App\Models\Movimiento;
use App\Models\MovimientoDetalle;
use App\Models\Producto;
use App\Models\TipoMovimiento;
use App\Models\TipoPago;
use Csgt\Crud\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
        $tipoMovimientos = TipoMovimiento::select('id', 'nombre', 'signo')->get();

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
                $codigo_barra = $request->codigo_barra;
                $bodega_id    = $request->bodega_id;
                $tipo         = json_decode($request->tipo);
                $producto     = self::dataProducto($codigo_barra, $bodega_id, $tipo);
                return response()->json($producto);
        }
    }

    public function dataProducto($codigoBarras, $bodegaId, $tipo)
    {

        $producto = Producto::query()
            ->where('codigo_barra', $codigoBarras)
            ->first();

        if (is_null($producto)) {
            abort(404, 'No se encontró el producto con el código de barras: ' . $codigoBarras);
        }

        if ($tipo->nombre == 'Entrada') {
            return $producto;
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
        if ($id == '0') {
            $misBodegas = auth()->user()
                ->bodegas
                ->map(function ($bodega) {
                    return ['id' => $bodega->id, 'nombre' => $bodega->nombre];
                });

            return [
                'id'                   => 0,
                'cliente_id'           => 0,
                'bodega_id'            => 0,
                'tipo_movimiento_id'   => 0,
                'estado_movimiento_id' => 0,
                'tipo_pago_id'         => 0,
                'recibe_bodega_id'     => 0,
                'voucher'              => '',
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
                'tipo_pago'            => [
                    'id'          => 1,
                    'descripcion' => 'Efectivo',
                ],
                'bodega_recibe'        => [
                    'id'     => 0,
                    'nombre' => '',
                ],
                'cliente'              => [
                    'id'        => '',
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
            ->with('detalles.producto')
            ->with('bodegaRecibe')
            ->with('cliente')
            ->findOrFail(Crypt::decrypt($id));

        return $movimiento;
    }

    public function store(Request $request)
    {
        $rules = [
            'movimiento.bodega.id'    => 'required',
            'movimiento.tipo.id'      => 'required',
            'movimiento.tipo_pago.id' => 'required',
            'movimiento.detalles'     => 'required|min:1',
        ];

        $request->validate($rules);

        $movimiento = $request->movimiento;

        if ($movimiento['tipo']['nombre'] == 'Traslado' && !$movimiento['bodega_recibe']) {
            abort(500, 'Debe seleccionar la bodega a la que se traslada el producto');
        }

        if ($movimiento['tipo']['nombre'] != 'Traslado' && $movimiento['cliente']['id'] == '') {
            abort(500, 'Debe ingresar el cliente');
        }

        if ($movimiento['tipo']['nombre'] == 'Venta' && $movimiento['tipo_pago']['nombre'] == 'Electrónico' && $movimiento['voucher'] == '') {
            abort(500, 'Debe ingresar el voucher');
        }

        if ($movimiento['tipo']['nombre'] == 'Traslado') {
            $movimiento['cliente']['id'] = 1;

            if ($movimiento['bodega_recibe']['id'] == 0) {
                abort(500, 'Debe seleccionar la bodega a la que se traslada el producto');
            }

            if ($movimiento['bodega_recibe']['id'] == $movimiento['bodega']['id']) {
                abort(500, 'La bodega de origen y destino no pueden ser iguales');
            }
        }

        DB::transaction(function () use ($movimiento) {
            $m                     = new Movimiento;
            $m->bodega_id          = $movimiento['bodega']['id'];
            $m->tipo_movimiento_id = $movimiento['tipo']['id'];
            $m->tipo_pago_id       = $movimiento['tipo_pago']['id'];

            if ($movimiento['tipo']['nombre'] == 'Traslado') {
                $m->recibe_bodega_id     = $movimiento['bodega_recibe']['id'];
                $m->estado_movimiento_id = 3;
            } else {
                $m->recibe_bodega_id     = $movimiento['bodega']['id'];
                $m->estado_movimiento_id = 1;
            }
            $m->cliente_id = $movimiento['cliente']['id'];
            $m->user_id    = auth()->user()->id;
            $m->voucher    = $movimiento['voucher'] ?? null;
            $m->save();

            switch ($movimiento['tipo']['nombre']) {
                case 'Entrada':
                    $this->storeDetalleEntrada($movimiento['detalles'], $m);
                    break;
                case 'Venta':
                    $this->storeDetalleVenta($movimiento['detalles'], $m);
                    break;
                case 'Traslado':
                    $this->storeTraslado($movimiento['detalles'], $m);
                    break;
            }

        });

        //TODO: crear factura en pdf, espero me de tiempo.
        return response()->json([
            'message' => $movimiento['tipo']['nombre'] . ' registrado(a) correctamente',
        ]);
    }

    public function storeDetalleEntrada($detalles, $movimiento)
    {
        foreach ($detalles as $detalle) {
            $lote                 = new Lote;
            $lote->producto_id    = $detalle['producto']['id'];
            $lote->bodega_id      = $movimiento->bodega_id;
            $lote->expiracion     = $detalle['expiracion'];
            $lote->cantidad       = $detalle['cantidad'];
            $lote->costo_unitario = $detalle['costo_unitario'];
            $lote->costo_total    = $detalle['cantidad'] * $detalle['costo_unitario'];
            $lote->existencia     = $detalle['cantidad'];
            $lote->save();

            $costoPromedio = Lote::where('producto_id', $lote->producto_id)
                ->where('existencia', '>', 0)
                ->avg('costo_unitario');

            $precio = $costoPromedio * 1.3; // 30% de ganancia

            // actualizar precio de venta
            $producto               = Producto::find($lote->producto_id);
            $producto->precio_venta = $precio;
            $producto->save();

            $md                      = new MovimientoDetalle;
            $md->movimiento_id       = $movimiento->id;
            $md->producto_id         = $lote->producto_id;
            $md->lote_id             = $lote->id;
            $md->cantidad            = $detalle['cantidad'];
            $md->precio_venta_unidad = $detalle['costo_unitario'];
            $md->sub_total           = $detalle['cantidad'] * $detalle['costo_unitario'];
            $md->save();
        }
    }

    public function storeDetalleVenta($detalles, $movimiento)
    {
        foreach ($detalles as $detalle) {
            $producto_id = $detalle['producto']['id'];
            $bodega_id   = $movimiento->bodega_id;
            $cantidad    = $detalle['cantidad'];
            $lotes       = [];

            $this->rebajarExistenciasLotes($producto_id, $bodega_id, $cantidad, $lotes);

            foreach ($lotes as $lote) {
                $md                      = new MovimientoDetalle;
                $md->movimiento_id       = $movimiento->id;
                $md->lote_id             = $lote['lote']->id;
                $md->producto_id         = $producto_id;
                $md->cantidad            = $lote['cantidad'];
                $md->precio_venta_unidad = $detalle['producto']['precio_venta'];
                $md->sub_total           = $detalle['producto']['precio_venta'] * $lote['cantidad'];
                $md->save();
            }
        }
    }

    public function rebajarExistenciasLotes(int $producto_id, int $bodega_id, float $cantidad, array&$lotes): void
    {
        $lote = Lote::query()
            ->where('bodega_id', $bodega_id)
            ->where('producto_id', $producto_id)
            ->where('existencia', '>', 0)
            ->orderBy('expiracion', 'asc')
            ->first();

        if (is_null($lote)) {
            abort(500, "No hay existencias del producto en la bodega");
        }

        if ($lote->existencia >= $cantidad) {
            $lote->existencia -= $cantidad;
            $lote->save();
            $lotes[] = ['lote' => $lote, 'cantidad' => $cantidad];
            return;
        } else {
            $lotes[] = ['lote' => $lote, 'cantidad' => $lote->existencia];
            $cantidad -= $lote->existencia;
            $lote->existencia = 0;
            $lote->save();

            $this->rebajarExistenciasLotes($producto_id, $bodega_id, $cantidad, $lotes);
        }
    }

    public function storeTraslado($detalles, $movimiento)
    {
        foreach ($detalles as $detalle) {
            $producto_id = $detalle['producto']['id'];
            $bodega_id   = $movimiento->bodega_id;
            $cantidad    = $detalle['cantidad'];
            $lotes       = [];

            $this->rebajarExistenciasLotes($producto_id, $bodega_id, $cantidad, $lotes);

            foreach ($lotes as $lote) {
                $md                      = new MovimientoDetalle;
                $md->movimiento_id       = $movimiento->id;
                $md->lote_id             = $lote['lote']->id;
                $md->producto_id         = $producto_id;
                $md->cantidad            = $lote['cantidad'];
                $md->precio_venta_unidad = $detalle['producto']['precio_venta'];
                $md->sub_total           = $detalle['producto']['precio_venta'] * $lote['cantidad'];
                $md->save();

                $l                 = new Lote;
                $l->producto_id    = $lote['lote']->producto_id;
                $l->bodega_id      = $movimiento->recibe_bodega_id;
                $l->expiracion     = $lote['lote']->expiracion;
                $l->cantidad       = $lote['cantidad'];
                $l->costo_unitario = $lote['lote']->costo_unitario;
                $l->costo_total    = $lote['cantidad'] * $lote['lote']->costo_unitario;
                $l->existencia     = $lote['cantidad'];
                $l->save();
            }
        }
    }
    // 'update'

}

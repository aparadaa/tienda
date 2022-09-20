<?php

use Database\Seeds\CsgtModule;

// new CsgtModule($aName, $aDescription, $aModule, $aMenuOrder, [$aIcon=null, $aParentModule=null, $aPermissions=CsgtModule::ALL, $aMenuPermission = 'index', $aLegacyModule])

class Sections
{
    public function get()
    {
        return collect([
            new CsgtModule('Inicio', 'Inicio', 'index', 1000, 'fa fa-home', null, CsgtModule::INDEX),
            new CsgtModule('Catálogos', '', 'catalogs', 10000, 'fa fa-book', null, []),
            new CsgtModule('Usuarios', 'Catálogos - Usuarios', 'catalogs.users', 100, 'fa fa-user', 'catalogs'),
            new CsgtModule('Roles', 'Catálogos - Roles', 'catalogs.roles', 200, 'fa fa-key', 'catalogs'),

            // Compras
            new CsgtModule('Compras', '', 'compras', 3000, 'fas fa-shopping-cart', null, []),
            new CsgtModule('Productos', 'Compras - Productos', 'compras.productos', 100, 'fas fa-shopping-bag', 'compras', ['index', 'create', 'store', 'edit', 'update', 'data', 'detail', 'show']),

            // Bodegas
            new CsgtModule('Bodegas', '', 'bodegas', 4000, 'fas fa-warehouse', null, []),
            new CsgtModule('Bodegas', 'Bodegas - Bodegas', 'bodegas.bodegas', 100, 'fas fa-warehouse', 'bodegas', ['index', 'create', 'store', 'edit', 'update', 'data', 'detail', 'show']),
            new CsgtModule('Bodegas de Usuarios', 'Bodegas - Bodegas de Usuarios', 'bodegas.bodegas-usuarios', 200, 'fas fa-house-user', 'bodegas', ['index', 'create', 'store', 'edit', 'update', 'data', 'detail', 'show']),
            new CsgtModule('Movimientos', 'Bodegas - Movimientos', 'bodegas.movimientos', 300, 'fas fa-folder-open', 'bodegas', ['index', 'create', 'store', 'edit', 'update', 'data', 'detail', 'show']),
            // new CsgtModule('Informes Bodegas', 'Bodegas - Informes Bodegas', 'bodegas.informesbodegas', 200, null, 'bodegas', ['show'], 'show', 7594),
            // new CsgtModule('Escritorio Pedidos', 'Bodegas - Escritorio Pedidos', 'bodegas.escritoriopedidos', 400, null, 'bodegas', ['show'], 'show', 7330),
            // new CsgtModule('Traslados Bodegas', 'Bodegas - Traslados Bodegas', 'bodegas.trasladosbodegas', 500, null, 'bodegas', ['show'], 'show', 7416),
            // new CsgtModule('Consulta Existencias', 'Bodegas - Consulta Existencias', 'bodegas.consultaexistencias', 600, null, 'bodegas', ['show'], 'show', 7516),
            // new CsgtModule('Descuentos Bodega', 'Bodegas - Descuentos Bodega', 'bodegas.descuentosbodega', 800, null, 'bodegas', ['show'], 'show', 7604),
            // new CsgtModule('Consulta Materiales', 'Bodegas - Consulta Materiales', 'bodegas.consultamateriales', 900, null, 'bodegas', ['show'], 'show', 7680),

            // new CsgtModule('Estiba', 'Escritorio fruta - Estibas', 'escritoriofruta.estibas', 500, null, 'escritoriofruta', ['index', 'store', 'update', 'destroy', 'data', 'detail']),
            // new CsgtModule('Movimientos', 'Bodegas - Movimientos - Movimientos', 'bodegas.movimientos.movimientos', 100, null, 'bodegas.movimientos'),
            // new CsgtModule('Salidas', 'Bodegas - Movimientos - Salidas', 'bodegas.movimientos.salidas', 200, null, 'bodegas.movimientos'),
            // new CsgtModule('Bodegas', 'Bodegas - Movimientos - Bodegas', 'bodegas.movimientos.bodegas', 300, 'fas fa-store', 'bodegas.movimientos', ['index', 'data', 'detail']),
            // new CsgtModule('Inventarios', 'Inventarios', 'inventarios', 1700, 'fas fa-newspaper', null, []),
            // new CsgtModule('Articulos', 'Inventarios - Articulos', 'inventarios.articulos', 100, 'fas fa-boxes', 'inventarios', ['index', 'store', 'detail']),
            // new CsgtModule('Recepcion', 'Bodegas - Recepcion', 'bodegas.recepcion', 200, 'fas fa-concierge-bell', 'bodegas'),
            // new CsgtModule('Saldos Solicitud', 'Inventarios - Saldos solicitud', 'inventarios.saldosolicitud', 200, 'fas fa-balance-scale-right', 'inventarios', ['index', 'detail']),
        ]);
    }
}

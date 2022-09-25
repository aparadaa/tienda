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

            // Inventarios
            new CsgtModule('Inventarios', '', 'inventarios', 5000, 'fas fa-clipboard-list', null, []),
            new CsgtModule('Inventarios', 'Inventarios - Inventarios', 'inventarios.inventarios', 100, 'fas fa-clipboard-list', 'inventarios', ['index', 'show']),
        ]);
    }
}

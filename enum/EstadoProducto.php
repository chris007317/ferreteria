<?php

namespace Enum;

enum EstadoProducto: string
{
    case PENDIENTE   = '000050';
    case REGISTRADO   = '000051';
    case EN_ALMACEN    = '000052';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
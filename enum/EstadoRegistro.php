<?php

namespace Enum;

enum EstadoRegistro: string
{
    case ACTIVO   = '000031';
    case INACTIVO   = '000032';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
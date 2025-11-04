<?php

namespace Enum;

enum EstadoCompra: string
{
    case PENDIENTE   = '000040';
    case APROBADO   = '000041';
    case RECIBIDO    = '000042';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
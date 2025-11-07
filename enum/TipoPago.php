<?php

namespace Enum;

enum TipoPago: string
{
    case EFECTIVO   = '000070';
    case YAPE   = '000071';
    case PLIN = '000072';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
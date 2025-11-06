<?php

namespace Enum;

enum TipoMovimiento: string
{
    case ENTRADA   = '000060';
    case SALIDA   = '000061';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
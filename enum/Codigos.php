<?php

namespace Enum;

enum Codigos: string
{
    case TIPO_DOCUMENTO   = '000010';
    case TIPO_USUARIO   = '000020';
    case ESTADO_REGISTRO    = '000030';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
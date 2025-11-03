<?php

namespace Enum;

enum TipoUsuario: string
{
    case ADMINISTRADOR   = '000020';
    case VENDEDOR   = '000021';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
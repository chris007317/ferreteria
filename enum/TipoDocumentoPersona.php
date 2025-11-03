<?php

namespace Enum;

enum TipoDocumentoPersona: string
{
    case DNI   = '000010';
    case PASAPORTE   = '000011';
    case CARNE_EXT    = '000012';

    public static function esValido(string $codigo): bool
    {
        return !is_null(self::tryFrom($codigo));
    }
}
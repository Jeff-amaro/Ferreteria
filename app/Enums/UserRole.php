<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrador = 'administrador';
    case Vendedor = 'vendedor';
    case Almacenero = 'almacenero';
    case Cliente = 'cliente';

    public function label(): string
    {
        return match ($this) {
            self::Administrador => 'Administrador',
            self::Vendedor => 'Vendedor',
            self::Almacenero => 'Almacenero',
            self::Cliente => 'Cliente',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Administrador => 'bg-primary-100 text-primary-700',
            self::Vendedor => 'bg-emerald-100 text-emerald-700',
            self::Almacenero => 'bg-amber-100 text-amber-700',
            self::Cliente => 'bg-slate-100 text-slate-700',
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}

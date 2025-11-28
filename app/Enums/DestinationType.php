<?php

namespace App\Enums;

enum DestinationType: string
{
    case GROUP = 'group';
    case CONTACT = 'contact';

    public function label(): string
    {
        return match ($this) {
            self::GROUP => 'Grupo',
            self::CONTACT => 'Contato',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn ($case) => [
                'value' => $case->value,
                'label' => $case->label(),
            ],
            self::cases()
        );
    }
}

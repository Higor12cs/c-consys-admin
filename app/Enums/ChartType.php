<?php

namespace App\Enums;

enum ChartType: string
{
    case SALES_MONTHLY = 'monthly_sales';
    case DELINQUENCY = 'delinquency';

    public function label(): string
    {
        return match ($this) {
            self::SALES_MONTHLY => 'Vendas Mensais',
            self::DELINQUENCY => 'Inadimplência',
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

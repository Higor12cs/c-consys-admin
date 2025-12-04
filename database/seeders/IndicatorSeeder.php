<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndicatorSeeder extends Seeder
{
    public function run()
    {
        $percentageIndicators = [
            '0119',
            '0120',
            '0121',
            '0122',
            '0123',
            '0124',
            '0125',
            '0126',
            '0127',
            '0128',
            '0301',
            '0307',
            '0308',
            '0309',
            '0310',
            '0311',
            '0312',
            '0459',
        ];

        $indicatorDescriptions = [
            '0101' => 'Vendas Mês',
            '0102' => 'Vendas Ano',
            '0103' => 'Proj. Vendas Mês Histórico',
            '0104' => 'Proj. Vendas Mês Dias Úteis',
            '0105' => 'Proj. Vendas Ano Histórico',
            '0106' => 'Proj. Vendas Ano Dias Úteis',
            '0107' => 'Vendas Mês Atual x Mês Ano Anterior',
            '0108' => 'Vendas Mês Atual x Mês Anterior',
            '0109' => 'Vendas Ano Atual x Ano Anterior',
            '0110' => 'Venda Mensal Crediário',
            '0111' => 'Venda Anual Crediário',
            '0112' => 'CMV Mês',
            '0113' => 'CMV Ano',
            '0114' => 'CMV Mês Proj. Histórico',
            '0115' => 'CMV Mês Proj. Dias Úteis',
            '0116' => 'CMV Ano Proj. Histórico',
            '0117' => 'CMV Ano Proj. Dias Úteis',
            '0118' => 'CMV Médio Últ. 12 Meses',
            '0119' => 'Mrg. CMV Mês',
            '0120' => 'Mrg. CMV Ano',
            '0121' => 'Mrg. Compras Mês',
            '0122' => 'Mrg. Compras Ano',
            '0123' => 'Mrg. Pagamento Mês',
            '0124' => 'Mrg. Pagamento Ano',
            '0125' => 'Mrg. Emissão CP Mês',
            '0126' => 'Mrg. Emissão CP Ano',
            '0127' => 'Mrg. Liq. Venc. Aberto Mês',
            '0128' => 'Mrg. Liq. Venc. Aberto Ano',
            '0129' => 'Ticket Médio Mês',
            '0130' => 'Ticket Médio Ano',
            '0131' => 'Clientes Atendidos Mês',
            '0132' => 'Clientes Atendidos Ano',
            '0133' => 'Pedidos Emitidos Mês',
            '0134' => 'Pedidos Emitidos Ano',
            '0201' => 'Entrada Mercadoria Mês',
            '0202' => 'Entrada Mercadoria Ano',
            '0301' => 'Inad. % Últ. 12 Meses',
            '0302' => 'Inad. R$ Últ. 12 Meses',
            '0303' => 'CR Própria A Receber',
            '0304' => 'CR Cartões A Receber',
            '0305' => 'Valor Recebido Mês',
            '0306' => 'Valor Recebido Ano',
            '0307' => 'Ind. Recebimento Mês',
            '0308' => 'Ind. Recebimento Ano',
            '0309' => '% Venda Cartão Mês',
            '0310' => '% Venda Cartão Ano',
            '0311' => '% Venda Pix Mês',
            '0312' => '% Venda Pix Ano',
            '0313' => 'Valor Venda Pix Mês',
            '0314' => 'Valor Venda Pix no Ano',
            '0315' => 'Valor Venda Cartão Mês',
            '0316' => 'Valor Venda Cartão Ano',
            '0317' => 'Valor Venda Cartão e Pix Mês',
            '0318' => 'Valor Venda Cartão e Pix Ano',
            '0401' => 'Pagamento Compras Mês',
            '0402' => 'Pagamento Compras Ano',
            '0403' => 'Pagamentos + Abertos Mês',
            '0404' => 'Pagamentos + Abertos Ano',
            '0405' => 'Valor Compras Aberto Total (Abertos Total)',
            '0406' => 'Valor Compras Aberto Total (Abertos Futuros)',
            '0407' => 'Empréstimos a Pagar Até 1 Ano',
            '0408' => 'Empréstimos a Pagar Geral',
            '0409' => 'Ind. Liquidez Imediata',
            '0410' => 'Valor Pró-Labore Mês',
            '0411' => 'Valor Pró-Labore Ano',
            '0412' => 'Empréstimo Pago Mês',
            '0413' => 'Empréstimo Pago Ano',
            '0414' => 'Investimento Pago Mês',
            '0415' => 'Investimento Pago Ano',
            '0416' => 'Entrada Capital Pago Mês',
            '0417' => 'Entrada Capital Pago Ano',
            '0418' => 'Pro-labore Aberto Até 1 Ano',
            '0419' => 'Pro-labore Aberto Total',
            '0420' => 'CMV Aberto Até 1 Ano',
            '0421' => 'CMV Aberto Total',
            '0422' => 'Investimento Aberto Até 1 Ano',
            '0423' => 'Investimento Aberto Total',
            '0501' => 'Valor Estoque',
            '0502' => 'Antigiro Estoque',
            '0601' => 'Condicionais Abertos Qtde.',
            '0602' => 'Condicionais Abertos R$',
            '0701' => 'OS Abertas Qtde.',
            '0702' => 'OS Abertas R$',
            '1001' => 'Saldos Finaceiros',
            '1002' => 'Lanc. Não Conciliados > 7 Dias',
        ];

        $rows = [];
        $now = now();
        foreach ($indicatorDescriptions as $code => $desc) {
            $rows[] = [
                'code' => str_pad($code, 4, '0', STR_PAD_LEFT),
                'description' => $desc,
                'is_percentage' => in_array($code, $percentageIndicators) ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('indicators')->upsert($rows, ['code'], ['description', 'is_percentage', 'updated_at']);
    }
}

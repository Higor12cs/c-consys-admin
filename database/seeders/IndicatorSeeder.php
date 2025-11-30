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
            '0101' => 'Vendas no Mês',
            '0102' => 'Vendas no Ano',
            '0103' => 'Venda no Mês Proj. Histórico de Venda',
            '0104' => 'Venda no Mês Proj. Dias Úteis no Mês',
            '0105' => 'Venda no Ano Proj. Histórico de Venda',
            '0106' => 'Venda no Ano Proj. Dias Úteis no Mês',
            '0107' => 'Venda Mês Atual x Mês Ano Anterior',
            '0108' => 'Venda Mês Atual x Mês Anterior',
            '0109' => 'Venda Ano Atual x Ano Anterior',
            '0110' => 'Venda Mensal no Crediario Próprio',
            '0111' => 'Venda Anual no Crediário Próprio',
            '0112' => 'CMV no Mês',
            '0113' => 'CMV no Ano',
            '0114' => 'CMV no Mês Proj. Histórico de Venda',
            '0115' => 'CMV no Mês Proj. Dias Úteis no Mês',
            '0116' => 'CMV no Ano Proj. Histórico de Venda',
            '0117' => 'CMV no Ano Proj. Dias Úteis no Mês',
            '0118' => 'CMV Médio dos Últimos 12 meses',
            '0119' => 'Mrg. CMV no Mês',
            '0120' => 'Mrg. CMV no Ano',
            '0121' => 'Mrg. Compras no Mês',
            '0122' => 'Mrg. Compras no Ano',
            '0123' => 'Mrg. Pagamento no Mês',
            '0124' => 'Mrg. Pagamento no Ano',
            '0125' => 'Mrg. Emissão CP no Mês',
            '0126' => 'Mrg. Emissão CP no Ano',
            '0127' => 'Mrg. Liq. + Venc. Aberto no Mês',
            '0128' => 'Mrg. Liq. + Venc. Aberto no Ano',
            '0129' => 'Ticket Médio no Mês',
            '0130' => 'Ticket Médio no Ano',
            '0131' => 'Clientes Atendidos no Mês',
            '0132' => 'Clientes Atendidos no Ano',
            '0133' => 'Pedidos Emitidos no Mês',
            '0134' => 'Pedidos Emitidos no Ano',
            '0201' => 'Entrada Mercadoria no Mês',
            '0202' => 'Entrada Mercadoria no Ano',
            '0301' => 'Inadimplência % Últ. 12 Meses',
            '0302' => 'Inadimplência R$ Últ. 12 Meses',
            '0303' => 'CR Própria A Receber',
            '0304' => 'CR Cartões A Receber',
            '0305' => 'Valor Recebido no Mês',
            '0306' => 'Valor Recebido no Ano',
            '0307' => 'Índice de Recebimento no Mês',
            '0308' => 'Índice de Recebimento no Ano',
            '0309' => 'Percentual da Venda em Cartão no Mês',
            '0310' => 'Percentual da Venda em Cartão no Ano',
            '0311' => 'Percentual da Venda em Pix no Mês',
            '0312' => 'Percentual da Venda em Pix no Ano',
            '0313' => 'Valor da Venda em Pix no Mês',
            '0314' => 'Valor da Venda em Pix no no Ano',
            '0315' => 'Valor da Venda em Cartão no Mês',
            '0316' => 'Valor da Venda em Cartão no Ano',
            '0317' => 'Valor da Venda em Cartão e Pix no Mês',
            '0318' => 'Valor da Venda em Cartão e Pix no Ano',
            '0401' => 'Pagamento Compras no Mês',
            '0402' => 'Pagamento Compras no Ano',
            '0403' => 'Pagamentos + Abertos no Mês',
            '0404' => 'Pagamentos + Abertos no Ano',
            '0405' => 'Valor Compras em Aberto Total (Abertos Total)',
            '0406' => 'Valor Compras em Aberto Total (Abertos Futuros)',
            '0407' => 'Empréstimos a Pagar Até 1 Ano',
            '0408' => 'Empréstimos a Pagar Geral',
            '0459' => 'Índice de Liquidez Imediata',
            '0460' => 'Valor do Pró-Labore no Mês',
            '0461' => 'Valor do Pró-Labore no Ano',
            '0462' => 'Empréstimo Pago no Mês',
            '0463' => 'Empréstimo Pago no Ano',
            '0464' => 'Investimento Pago no Mês',
            '0465' => 'Investimento Pago no Ano',
            '0466' => 'Entrada Capital Pago no Mês',
            '0467' => 'Entrada Capital Pago no Ano',
            '0468' => 'Pro-labore Aberto até um  Ano',
            '0469' => 'Pro-labore Aberto Total',
            '0470' => 'CMV Aberto Até um Ano',
            '0471' => 'CMV Aberto Total',
            '0472' => 'Investimento Aberto Até um Ano',
            '0473' => 'Investimento Aberto Total',
            '0501' => 'Valor do Estoque Atual',
            '0502' => 'Antigiro do Estoque Atual',
            '0601' => 'Condicionais Abertos Quantitativo',
            '0602' => 'Condicionais Abertos em Reais',
            '0701' => 'OS Abertas Quantitativo',
            '0702' => 'OS Abertas em Reais',
            '1001' => 'Saldos Finaceiros',
            '1002' => 'Lançamentos Não Conciliados > 7 Dias',
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

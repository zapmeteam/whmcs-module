<?php

namespace ZapMe\Whmcs\Helper\Template;

class InvoiceCreatedStructure extends AbstractTemplateStructure
{
    protected function base(): array
    {
        return [
            'name'        => 'Fatura Criada',
            'description' => 'Modelo de mensagem enviada quando uma fatura é criada pelo WHMCS',
            'paghiper'    => true,
        ];
    }

    protected function rules(): array
    {
        return [
            'rules' => [
                [
                    'id'          => 'controlByClient',
                    'label'       => 'Controle de Envio por ID de Cliente',
                    'description' => 'Aborta o envio se o id do cliente for um dos determinados <b>(use virgula para mais de um)</b>',
                    'field'       => [
                        'type' => 'text',
                    ]
                ],
                [
                    'id'          => 'controlByMinimalValue',
                    'label'       => 'Controle de Envio por Valor Mínimo',
                    'description' => 'Aborta o envio se o valor da fatura for <b>inferior</b> ao valor definido',
                    'field'       => [
                        'type' => 'text',
                    ]
                ],
                [
                    'id'          => 'controlByWeekDay',
                    'label'       => 'Controle de Envio por Dia da Semana',
                    'description' => 'Aborta o envio se o dia do envio for um dos determinados <b>(dom: 0, seg: 1, ter: 2, qua: 3, qui: 4, sex: 5, sab: 6 - use virgula para mais de um)</b>',
                    'field'       => [
                        'type' => 'text',
                    ]
                ],
                [
                    'id'          => 'controlByGateway',
                    'label'       => 'Controle de Envio por Gateway',
                    'description' => 'Aborta o envio se o gateway para pagamento for um dos determinados <b>(insira o codinome do gateway - verifique no banco de dados, tabela: tblinvoices, coluna: paymentmethod - use virgula para mais de um)</b>',
                    'field'       => [
                        'type' => 'text',
                    ]
                ]
            ]
        ];
    }

    protected function variables(): array
    {
        return [
            'variable' => [
                'invoiceid'        => 'Id da fatura',
                'duedate'          => 'Vencimento da fatura (d/m/Y)',
                'value'            => 'Valor total',
                'paghiper_barcode' => 'Código de Barras do Boleto Bancário da PagHiper',
            ]
        ];
    }
}

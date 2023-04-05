<?php

namespace ZapMe\Whmcs\Helper\Template;

class TemplateRule
{
    public static function build(string $rule): array
    {
        return (new self())->{$rule}();
    }

    private function client(): array
    {
        return [
            'id'          => 'client',
            'label'       => 'Controle de Envio por Cliente (ID)',
            'description' => 'Aborta o envio se o id do cliente for um dos determinados <b>(use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }

    private function status(): array
    {
        return [
            'id'          => 'status',
            'label'       => 'Controle de Envio por Status do Cliente',
            'description' => 'Aborta o envio se o status do cliente for igual ao selecionado <b>(ou qualquer para qualquer status)</b>',
            'field'       => [
                'type' => 'select',
                'options' => [
                    'Ativo',
                    'Inativo',
                    'Fechado',
                    'Qualquer'
                ]
            ]
        ];
    }

    private function value(): array
    {
        return [
            'id'          => 'value',
            'label'       => 'Controle de Envio por Valor Mínimo',
            'description' => 'Aborta o envio se o valor da fatura for <b>inferior</b> ao valor definido',
            'field'       => ['type' => 'text']
        ];
    }

    private function weekdays(): array
    {
        return [
            'id'          => 'weekdays',
            'label'       => 'Controle de Envio por Dia da Semana',
            'description' => 'Aborta o envio se o dia do envio for um dos determinados <b>(dom: 0, seg: 1, ter: 2, qua: 3, qui: 4, sex: 5, sab: 6 - use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }

    private function gateways(): array
    {
        return [
            'id'          => 'gateways',
            'label'       => 'Controle de Envio por Gateway',
            'description' => 'Aborta o envio se o gateway para pagamento for um dos determinados <b>(insira o codinome do gateway - verifique no banco de dados, tabela: tblinvoices, coluna: paymentmethod - use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }

    private function departments(): array
    {
        return [
            'id'          => 'deppartments',
            'label'       => 'Controle de Envio por Departamento',
            'description' => 'Aborta o envio se o id do departamento do ticket for um dos determinados <b>(use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }

    private function admin(): array
    {
        return [
            'id'          => 'admin',
            'label'       => 'Controle de Envio por Nome do Membro da Equipe',
            'description' => 'Aborta o envio se o nome do membro da equipe que responder o ticket for um dos determinados <b>(nome ou uma parte do nome - use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }

    private function server(): array
    {
        return [
            'id'          => 'server',
            'label'       => 'Controle de Envio por Id do Servidor',
            'description' => 'Aborta o envio se o id do servidor for um dos determinados <b>(use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }

    private function product(): array
    {
        return [
            'id'          => 'product',
            'label'       => 'Controle de Envio por Id de Produto',
            'description' => 'Aborta o envio se o id do produto for um dos determinados <b>(use virgula para mais de um)</b>',
            'field'       => ['type' => 'text']
        ];
    }
}

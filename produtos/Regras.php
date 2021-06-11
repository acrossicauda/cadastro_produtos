<?php

setlocale(LC_MONETARY, 'pt_BR', 'ptb');
/**
 * Class responsavel por tratar as regras de desconto e convert de valores
 */
class Regras {

    /**
     * Faz a conversao para Real
     * @param $format = string
     * @param $number = string
     */
    public function moneyFormat($format = '', $number = '')
    {
        if(empty($number)) {
            return $number;
        }
        $format = numfmt_format_currency(numfmt_create('pt_BR', NumberFormatter::CURRENCY), $number, "ptb");
        $format = preg_replace("/[^0-9,.]/", "", $format);
        return $format;
    }

    /**
     * Retorna a porcentagem de desconto de acordo com a cor e valor
    * @param $dados = array
     */
    public function getDesconto(Array $dados = array())
    {
        if(!empty($dados['cor'])) {
            $desconto = '';
            switch($dados['cor']) {
                case 'vermelho' && $dados['preco'] > 50:
                    $desconto = '5%';
                    if($dados['preco'] > 50) {
                    }
                    break;
                case 'azul':
                case 'vermelho':
                    $desconto = '20%';
                    break;
                case 'amarelo':
                    $desconto = '10%';
                    break;
                default:
                    $desconto = '-';

            }
        }
        return $desconto;
    }

}
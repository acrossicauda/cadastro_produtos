<?php

include_once './conexao.php';
require_once './Regras.php';

class Produtos {

    private $conn;
    public $regra;
    public function __construct() {
        $this->conn = Conexao::getInstance();
        $this->regra = new Regras();
    }

    /**
    * Busca os produtos e precos
    * se for utilizar algum filtro deve passar no array contendo 
    * chave = campo e valor = filtro
    * @param $filters = array => ex.: ['nome' => 'Tiago', 'cor' => 'Azul', 'valor' => '10.00']
    */
    public function getProdutos(Array $filters = array())
    {
        $query = "SELECT pd.*, p.*
            FROM produtos as pd
            LEFT JOIN precos as p on pd.idpreco = p.idpreco";
        if(!empty($filters )) {
            $where = array();
            foreach($filters as $k => $v) {
                if(empty($v['value'])) 
                    continue;
                $comp = '=';
                if(!empty($v['tipo'])) {
                    $comp = $v['tipo'];
                }
                $where[] = "{$k} {$comp} '{$v['value']}'";
            }
            if(!empty($where))
                $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $resp = $this->conn->selectQuery($query);
        return (empty($resp)) ? [] : $resp;
    }


    /**
    * faz o insert dos produtos e do preço, é necessario fazer o insert 
    * do produto primeiro para pegar o id
    * @param $dados = array => ['nome' => '', 'cor' => '', 'valor' => '']
    */
    public function setProdutos(Array $dados = array())
    {
        $dadosPreco = [
            'preco' => $dados['preco']
        ];
        $query = "INSERT INTO precos(preco)
                    VALUES (" . implode(',', $dadosPreco) . ")";

        $resp = $this->conn->execute($query);

        $dadosProd = [
            'nome'  => "'" . $dados['nome'] . "'",
            'cor'  => "'" . $dados['cor']. "'",
            'idpreco' => $resp['message']
        ];

        if($resp['success']) {
            $query = "INSERT INTO produtos(nome, cor, idpreco)
                    VALUES (" . implode(',', $dadosProd) . ")";
            
            $resp = $this->conn->execute($query);
        }

        return ['success' => $resp['success'], 'message' => $resp['message']];
    }

    /**
     * Atualiza os produtos e precos, os dados do update e ids de 
     * produto e preco devem ser passados no array $dados
     * @param @dados => array
     */
    public function updateProdutos(Array $dados = array())
    {
        $query = "UPDATE produtos, precos 
            SET produtos.nome = '{$dados['nome']}'";
        if(!empty($dados['cor'])) {
            $query .= ", produtos.cor = '{$dados['cor']}'";
        }
        if(!empty($dados['preco'])) {
            $preco = $this->regra->moneyFormat('%.2n', $dados['preco']);
            $preco = str_replace(",", ".", $preco);
            $query .= ",precos.preco = {$preco}";
        }
        if($dados['idpreco']) {
            $query .= " WHERE precos.idpreco = {$dados['idpreco']} AND produtos.idprod = {$dados['idprod']}";
        } else {
            $query .= " WHERE produtos.idprod = {$dados['idprod']}";
        }

        $resp = $this->conn->execute($query);

        return ['success' => $resp['success'], 'message' => $resp['message']];
    }


    /**
     * Deleta os dados da tabela produtos e precos
     * @param $idProduto => int
     * @param $idpreco => int
     */
    public function deleteProdutos(int $idProduto, int $idpreco)
    {
        $queryPro = "DELETE produtos, precos 
        FROM produtos LEFT JOIN precos ON produtos.idpreco = precos.idpreco";

        if(trim($idpreco) != '') {
            $queryPro .= " WHERE produtos.idpreco = $idpreco";
        } else {
            $queryPro .= " WHERE produtos.idprod = $idProduto";
        }
        
        $resp = $this->conn->execute($queryPro);

        return ['success' => $resp['success'], 'message' => $resp['message']];

    }

    public function setPost(Array $post = array())
    {
        if(!empty($post['btn_novo'])) {
            // faz a conversao dos campos que vem por post para fazer o insert
            $dataInsert = [
                'nome' => $post['novo_produto'],
                'cor' => $post['novo_cor'],
                'preco' => $post['novo_preco'],
            ];

            $ok = $this->setProdutos($dataInsert);
        } else if(!empty($post['btn_deletar'])) {
            // para o delete e necessario passar os idproduto e o idpreco
            $ok = $this->deleteProdutos($post['idproduto'], $post['idpreco']);
        } else if(!empty($post['btn_editar'])) {
            // faz a conversao dos campos que vem por post para fazer o update
            $dataUpdate = [
                'nome' => $post['nome'],
                'cor' => $post['cor'],
                'preco' => $post['preco'],
                'idprod' => $post['idproduto'],
                'idpreco' => $post['idpreco'],
            ];
            $ok = $this->updateProdutos($dataUpdate);
        } else {
            $tipoPreco = '';

            /**
             * monta um array com os filtros
             */
            if(!empty($post['filter_preco_tipo'])) {
                if($post['filter_preco_tipo'] == 'maior') {
                    $tipoPreco = '>';
                } else if($post['filter_preco_tipo'] == 'menor') {
                    $tipoPreco = '<';
                } else {
                    $tipoPreco = '=';
                }
            }
            $filters = [
                'nome' => ['value' => $post['filter_nome']],
                'cor' => ['value' => $post['filter_cor']],
                'preco' => ['value' => $post['filter_preco'], 'tipo' => $tipoPreco],
            ];
            return $filters;
        }
    }
}
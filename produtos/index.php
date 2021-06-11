<?php

require_once './Produtos.php';
require_once './Regras.php';
$produtos = new Produtos();

$filters = array();
if(!empty($_POST)) {
    $prodResp = $produtos->setPost($_POST);

    // o post sera usado como 'cache' pros filtros
    if(empty($_POST['filters'])) {
        unset($_POST);
    } else {
        $filters = $prodResp;
    }
}

$prod = $produtos->getProdutos($filters);

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos</title>
    <link rel="Produtos/css/stylesheet" href="style.css">
    <script src="Produtos/js/scripts.js"></script>
</head>
<body>

    <form name="frm_produtos" method="post">
        <table id="filters" well>
            <tr>
                <th>Produtos</th>
                <th>Cor</th>
                <th>Preços</th>
            </tr>
        <tr>
            <td><input type="text" name="filter_nome" id="filter_nome" value="<?= (isset($_POST['filter_nome'])) ? $_POST['filter_nome']: '' ?>"></td>
            <td>
                <select name="filter_cor" id="filter_cor">
                    <option value="">-- Filtrar Cor --</option>
                    <option value="azul" <?= (isset($_POST['filter_cor']) && $_POST['filter_cor'] == 'azul') ? 'selected' : '' ?> >Azul</option>
                    <option value="amarelo" <?= (isset($_POST['filter_cor']) && $_POST['filter_cor'] == 'amarelo') ? 'selected' : '' ?> >Amarelo</option>
                    <option value="vermelho" <?= (isset($_POST['filter_cor']) && $_POST['filter_cor'] == 'vermelho') ? 'selected' : '' ?> >Vermelho</option>
                </select>
            </td>
            <td>
                <select name="filter_preco_tipo" id="filter_preco_tipo">
                    <option value="">Filtro de valor</option>
                    <option value="maior">Maior que</option>
                    <option value="menor">Menor que</option>
                    <option value="igual">Igual a</option>
                </select>
                <input type="number" step="0.01" name="filter_preco" id="filter_preco" value="<?= isset($_POST['filter_preco']) ? $_POST['filter_preco']: '' ?>"></td>
            <td><button name="filters" value="filters">Buscar</button></td>
        </tr>
        </table>
    </form>
    <br>
    <table id="tableProdutos">
        <tr>
            <th>Produtos</th>
            <th>Preços</th>
            <th>Cor</th>
            <th>Desconto</th>
        </tr>
        <?php if(empty($prod)) : ?>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        <?php else : ?>
        <?php foreach($prod as $key => $value) : ?>
        <form name="frm_produtos" method="post">
            <tr>
                <td>
                    <input type="text" name="nome" id="nome" value="<?= $value['nome'] ?>">
                </td>
                <td>
                    <?php 
                        $preco = $produtos->regra->moneyFormat('%,2n', $value['preco']);
                        $desconto = $produtos->regra->getDesconto($value);
                    ?>
                    R$<input type="text" step="0.01" name="preco" id="preco" value="<?= $preco ?>">
                </td>
                <td>
                    <?php if(trim($value['cor']) != '') : ?>
                    <input type="text" name="cor" value="<?= $value['cor'] ?>" disabled>
                    <?php else : ?>
                    <select name="cor" id="cor">
                        <option value="">-- SELECIONE --</option>
                        <option value="azul">Azul</option>
                        <option value="amarelo">Amarelo</option>
                        <option value="vermelho">Vermelho</option>
                    </select>
                </td>
                <?php endif; ?>
                <td><input type="text" value="<?= $desconto ?>" disabled></td>
                <td>
                    <input type="hidden" name="idproduto" name="idproduto" value="<?= $value['idprod'] ?>">
                    <input type="hidden" name="idpreco" name="idpreco" value="<?= $value['idpreco'] ?>">
                    <input type="submit" name="btn_editar" value="Editar"><button name="btn_deletar" value="delete">deletar</button>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</form>
<br>
<form name="frm_produtos_novos" method="post">
    <div>
        <h2>Adicionar Novo:</h2>
        <input type="text" placeholder="Produto" name="novo_produto" id="novo_produto" required="">
        <input type="number" step="0.01" placeholder="Preço" name="novo_preco" id="novo_preco"  required="">
                    <select name="novo_cor" id="novo_cor">
                        <option value="">-- SELECIONE --</option>
                        <option value="azul">Azul</option>
                        <option value="amarelo">Amarelo</option>
                        <option value="vermelho">Vermelho</option>
                    </select>
    </div>
    <input type="submit" value="Adicionar Novo" name="btn_novo">
</form>
    
</body>
</html>
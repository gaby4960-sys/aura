<?php
session_start();
require_once "config/conexao.php";

// Verificar se o cliente está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Verificar se o carrinho está vazio
if (empty($_SESSION['carrinho'])) {
    header("Location: carrinho.php");
    exit;
}

$mensagem = "";
$sucesso = false;
$itens = [];
$total = 0;

// Buscar os produtos do carrinho
foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto) {

        $produto['quantidade'] = $quantidade;
        $produto['subtotal'] = $produto['preco'] * $quantidade;

        $total += $produto['subtotal'];

        $itens[] = $produto;
    }
}

// Confirmar pedido
if (isset($_POST['confirmar'])) {

    $pagamento = $_POST['pagamento'] ?? '';

    if (!in_array($pagamento, ['PIX', 'Boleto', 'Cartão'], true)) {

        $mensagem = "Escolha uma forma de pagamento válida.";

    } else {

        try {

            $conexao->beginTransaction();

            $total = 0;
            $itensPedido = [];

            // Conferir novamente o estoque
            foreach ($_SESSION['carrinho'] as $id => $quantidade) {

                $consulta = $conexao->prepare(
                    "SELECT * FROM produtos
                     WHERE id = ? FOR UPDATE"
                );

                $consulta->execute([$id]);

                $produto = $consulta->fetch(PDO::FETCH_ASSOC);

                if (!$produto || $quantidade < 1 ||
                    $quantidade > $produto['estoque']) {

                    throw new Exception(
                        "Um dos produtos está com estoque insuficiente."
                    );
                }

                $produto['quantidade'] = $quantidade;

                $total += $produto['preco'] * $quantidade;

                $itensPedido[] = $produto;
            }

            // Registrar o pedido
            $sql = "INSERT INTO pedidos
                    (usuario_id, forma_pagamento, total)
                    VALUES (?, ?, ?)";

            $consulta = $conexao->prepare($sql);

            $consulta->execute([
                $_SESSION['usuario_id'],
                $pagamento,
                $total
            ]);

            $pedidoId = $conexao->lastInsertId();

            // Registrar os itens do pedido
            foreach ($itensPedido as $item) {

                $sql = "INSERT INTO itens_pedido
                        (pedido_id, produto_id, quantidade, preco_unitario)
                        VALUES (?, ?, ?, ?)";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    $pedidoId,
                    $item['id'],
                    $item['quantidade'],
                    $item['preco']
                ]);

                // Descontar do estoque
                $sql = "UPDATE produtos
                        SET estoque = estoque - ?
                        WHERE id = ?";

                $consulta = $conexao->prepare($sql);

                $consulta->execute([
                    $item['quantidade'],
                    $item['id']
                ]);
            }

            $conexao->commit();

            // Limpar o carrinho
            $_SESSION['carrinho'] = [];

            $sucesso = true;

        } catch (Exception $e) {

            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }

            $mensagem = "Não foi possível finalizar a compra. Confira o estoque e tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Finalizar compra - Aura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">

</head>

<body>

<nav class="navbar">

    <div class="container">

        <a class="navbar-brand" href="index.php">
            Aura 🌷
        </a>

        <div class="d-flex gap-3">

            <a class="nav-link" href="index.php">Início</a>

            <a class="nav-link" href="carrinho.php">Carrinho</a>

            <a class="nav-link" href="logout.php">Sair</a>

        </div>

    </div>

</nav>

<div class="container">

    <div class="caixa-finalizar">

        <?php if ($sucesso): ?>

            <div class="text-center">

                <h2>Pedido realizado com sucesso! 🌷</h2>

                <p class="mt-3">
                    Obrigada por escolher a Aura!
                </p>

                <p>
                    Número do pedido: <?= $pedidoId ?>
                </p>

                <p>
                    Forma de pagamento:
                    <?= htmlspecialchars($pagamento) ?>
                </p>

                <p class="preco">
                    Total: R$ <?= number_format($total, 2, ',', '.') ?>
                </p>

                <a href="index.php"
                   class="btn btn-aura w-100">

                    Voltar para a loja

                </a>

            </div>

        <?php else: ?>

            <h2 class="text-center mb-4">
                Finalizar compra
            </h2>

            <p>
                Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!
            </p>

            <?php if ($mensagem != ""): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($mensagem) ?>
                </div>

            <?php endif; ?>

            <hr>

            <h5>Resumo do pedido</h5>

            <?php foreach ($itens as $item): ?>

                <div class="d-flex justify-content-between mb-3">

                    <span>

                        <?= htmlspecialchars($item['nome']) ?>

                        x <?= $item['quantidade'] ?>

                    </span>

                    <span>

                        R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>

                    </span>

                </div>

            <?php endforeach; ?>

            <hr>

            <h4 class="preco text-end">

                Total: R$ <?= number_format($total, 2, ',', '.') ?>

            </h4>

            <form method="POST">

                <h5 class="mt-4 mb-3">
                    Forma de pagamento
                </h5>

                <div class="opcao-pagamento">

                    <input type="radio"
                           name="pagamento"
                           value="PIX"
                           id="pix"
                           required>

                    <label for="pix">PIX</label>

                </div>

                <div class="opcao-pagamento">

                    <input type="radio"
                           name="pagamento"
                           value="Boleto"
                           id="boleto"
                           required>

                    <label for="boleto">Boleto</label>

                </div>

                <div class="opcao-pagamento">

                    <input type="radio"
                           name="pagamento"
                           value="Cartão"
                           id="cartao"
                           required>

                    <label for="cartao">Cartão</label>

                </div>

                <button type="submit"
                        name="confirmar"
                        class="btn btn-aura w-100 mt-3">

                    Confirmar pedido

                </button>

            </form>

            <a href="carrinho.php"
               class="btn btn-outline-secondary w-100 mt-3">

                Voltar ao carrinho

            </a>

            <p class="text-center small mt-3">

                Pagamento simulado para fins educacionais.

            </p>

        <?php endif; ?>

    </div>

</div>

<footer>
    Aura - Velas Aromáticas © 2026
</footer>

</body>
</html>
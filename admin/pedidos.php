<?php
session_start();
require_once "../config/conexao.php";

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$sql = "SELECT pedidos.*, usuarios.nome AS cliente
        FROM pedidos
        INNER JOIN usuarios
        ON pedidos.usuario_id = usuarios.id
        ORDER BY pedidos.id DESC";

$pedidos = $conexao->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pedidos - Aura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/estilo.css">

</head>

<body>

<nav class="navbar">

    <div class="container">

        <a class="navbar-brand" href="index.php">
            Aura 🌷
        </a>

        <div class="d-flex gap-3">

            <a class="nav-link" href="index.php">Painel</a>

            <a class="nav-link" href="produtos.php">Produtos</a>

            <a class="nav-link" href="../logout.php">Sair</a>

        </div>

    </div>

</nav>

<div class="container mt-5 mb-5">

    <h2 class="text-center mb-4">
        Pedidos realizados
    </h2>

    <?php if (empty($pedidos)): ?>

        <div class="caixa-admin text-center">

            Nenhum pedido realizado até o momento.

        </div>

    <?php endif; ?>

    <?php foreach ($pedidos as $pedido): ?>

        <div class="caixa-admin mb-4">

            <h4>
                Pedido #<?= $pedido['id'] ?>
            </h4>

            <p>
                <strong>Cliente:</strong>
                <?= htmlspecialchars($pedido['cliente']) ?>
            </p>

            <p>
                <strong>Data:</strong>
                <?= date('d/m/Y H:i', strtotime($pedido['data_pedido'])) ?>
            </p>

            <p>
                <strong>Pagamento:</strong>
                <?= htmlspecialchars($pedido['forma_pagamento']) ?>
            </p>

            <?php

            $consulta = $conexao->prepare(
                "SELECT itens_pedido.*, produtos.nome
                 FROM itens_pedido
                 INNER JOIN produtos
                 ON itens_pedido.produto_id = produtos.id
                 WHERE itens_pedido.pedido_id = ?"
            );

            $consulta->execute([$pedido['id']]);

            $itens = $consulta->fetchAll(PDO::FETCH_ASSOC);

            ?>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Preço unitário</th>
                            <th>Subtotal</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($itens as $item): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($item['nome']) ?>
                                </td>

                                <td>
                                    <?= $item['quantidade'] ?>
                                </td>

                                <td>
                                    R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?>
                                </td>

                                <td>
                                    R$ <?= number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <h5 class="preco text-end">

                Total: R$ <?= number_format($pedido['total'], 2, ',', '.') ?>

            </h5>

        </div>

    <?php endforeach; ?>

    <a href="index.php"
       class="btn btn-outline-secondary">

        Voltar ao painel

    </a>

</div>

<footer>
    Aura - Velas Aromáticas © 2026
</footer>

</body>
</html>
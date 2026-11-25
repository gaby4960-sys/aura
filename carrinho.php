<?php
session_start();
require_once "config/conexao.php";

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$mensagem = "";

// Adicionar produto
if (isset($_POST['adicionar'])) {

    $id = (int) $_POST['produto_id'];
    $quantidade = max(1, (int) $_POST['quantidade']);

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);
    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto) {

        $atual = $_SESSION['carrinho'][$id] ?? 0;
        $novaQuantidade = $atual + $quantidade;

        if ($novaQuantidade <= $produto['estoque']) {

            $_SESSION['carrinho'][$id] = $novaQuantidade;

            $mensagem = "Produto adicionado ao carrinho!";

        } else {

            $mensagem = "Quantidade superior ao estoque disponível!";
        }
    }
}

// Atualizar quantidade
if (isset($_POST['atualizar'])) {

    $id = (int) $_POST['produto_id'];
    $quantidade = max(1, (int) $_POST['quantidade']);

    $consulta = $conexao->prepare(
        "SELECT estoque FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);
    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto && isset($_SESSION['carrinho'][$id])) {

        if ($quantidade <= $produto['estoque']) {

            $_SESSION['carrinho'][$id] = $quantidade;

            $mensagem = "Quantidade atualizada!";

        } else {

            $mensagem = "Estoque insuficiente!";
        }
    }
}

// Remover produto
if (isset($_POST['remover'])) {

    $id = (int) $_POST['produto_id'];

    unset($_SESSION['carrinho'][$id]);

    $mensagem = "Produto removido do carrinho!";
}

// Buscar produtos do carrinho
$itens = [];
$total = 0;
$estoqueValido = true;

foreach ($_SESSION['carrinho'] as $id => $quantidade) {

    $consulta = $conexao->prepare(
        "SELECT * FROM produtos WHERE id = ?"
    );

    $consulta->execute([$id]);

    $produto = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($produto) {

        $produto['quantidade'] = $quantidade;
        $produto['subtotal'] = $produto['preco'] * $quantidade;

        if ($quantidade > $produto['estoque']) {
            $estoqueValido = false;
        }

        $total += $produto['subtotal'];

        $itens[] = $produto;

    } else {

        unset($_SESSION['carrinho'][$id]);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Carrinho - Aura</title>

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

            <?php if (isset($_SESSION['usuario_id'])): ?>

                <a class="nav-link" href="logout.php">Sair</a>

            <?php else: ?>

                <a class="nav-link" href="login.php">Entrar</a>

            <?php endif; ?>

        </div>

    </div>

</nav>

<div class="container mt-5 mb-5">

    <h2 class="text-center mb-4">
        Meu carrinho
    </h2>

    <?php if ($mensagem != ""): ?>

        <div class="alert alerta-aura">
            <?= htmlspecialchars($mensagem) ?>
        </div>

    <?php endif; ?>

    <?php if (empty($itens)): ?>

        <div class="caixa-carrinho text-center">

            <h4>Seu carrinho está vazio!</h4>

            <p>Conheça nossas velas aromáticas.</p>

            <a href="index.php" class="btn btn-aura">
                Continuar comprando
            </a>

        </div>

    <?php else: ?>

        <div class="caixa-carrinho">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($itens as $item): ?>

                        <tr>

                            <td>

                                <img
                                    src="img/<?= htmlspecialchars($item['imagem']) ?>"
                                    class="foto-carrinho"
                                    alt="<?= htmlspecialchars($item['nome']) ?>"
                                >

                                <?= htmlspecialchars($item['nome']) ?>

                                <?php if ($item['quantidade'] > $item['estoque']): ?>

                                    <p class="text-danger">
                                        Estoque insuficiente
                                    </p>

                                <?php endif; ?>

                            </td>

                            <td>
                                R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                            </td>

                            <td>

                                <form method="POST"
                                      class="d-flex gap-2">

                                    <input
                                        type="hidden"
                                        name="produto_id"
                                        value="<?= $item['id'] ?>"
                                    >

                                    <input
                                        type="number"
                                        name="quantidade"
                                        class="form-control quantidade-carrinho"
                                        min="1"
                                        max="<?= $item['estoque'] ?>"
                                        value="<?= $item['quantidade'] ?>"
                                        required
                                    >

                                    <button
                                        type="submit"
                                        name="atualizar"
                                        class="btn btn-aura btn-sm"
                                    >
                                        Atualizar
                                    </button>

                                </form>

                            </td>

                            <td>
                                R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>
                            </td>

                            <td>

                                <form method="POST">

                                    <input
                                        type="hidden"
                                        name="produto_id"
                                        value="<?= $item['id'] ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="remover"
                                        class="btn btn-remover btn-sm"
                                    >
                                        Remover
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

        <div class="caixa-carrinho mt-4 text-end">

            <h4>
                Total: R$ <?= number_format($total, 2, ',', '.') ?>
            </h4>

            <a href="index.php"
               class="btn btn-outline-secondary mt-3">
                Continuar comprando
            </a>

            <?php if ($estoqueValido): ?>

                <a href="finalizar.php"
                   class="btn btn-aura mt-3">
                    Finalizar compra
                </a>

            <?php else: ?>

                <p class="text-danger mt-3">
                    Atualize ou remova os produtos sem estoque suficiente.
                </p>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>

<footer>
    Aura - Velas Aromáticas © 2026
</footer>

</body>
</html>
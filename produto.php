<?php
session_start();
require_once "config/conexao.php";

$id = (int) ($_GET['id'] ?? 0);

$sql = "SELECT produtos.*, categorias.nome AS categoria
        FROM produtos
        INNER JOIN categorias
        ON produtos.categoria_id = categorias.id
        WHERE produtos.id = ?";

$consulta = $conexao->prepare($sql);
$consulta->execute([$id]);

$produto = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Aura - Detalhes do produto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<nav class="navbar">
    <div class="container">

        <a class="navbar-brand" href="index.php">
            Aura 
        </a>

        <div class="d-flex gap-3">
            <a class="nav-link" href="index.php">Início</a>
            <a class="nav-link" href="carrinho.php">Carrinho</a>
        </div>

    </div>
</nav>

<div class="container mt-5 mb-5">

    <div class="row align-items-center g-5">

        <div class="col-md-6">

            <img
                src="img/<?= htmlspecialchars($produto['imagem']) ?>"
                class="img-fluid foto-produto"
                alt="<?= htmlspecialchars($produto['nome']) ?>"
            >

        </div>

        <div class="col-md-6">

            <p class="categoria-produto">
                <?= htmlspecialchars($produto['categoria']) ?>
            </p>

            <h2>
                <?= htmlspecialchars($produto['nome']) ?>
            </h2>

            <p class="mt-3">
                <?= htmlspecialchars($produto['descricao']) ?>
            </p>

            <p class="preco">
                R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
            </p>

            <?php if ($produto['estoque'] > 0): ?>

                <p class="text-success">
                    <?= $produto['estoque'] ?> unidades disponíveis
                </p>

                <form action="carrinho.php" method="POST">

                    <input type="hidden" name="produto_id"
                           value="<?= $produto['id'] ?>">

                    <label class="form-label">
                        Quantidade:
                    </label>

                    <input
                        type="number"
                        name="quantidade"
                        class="form-control mb-3"
                        min="1"
                        max="<?= $produto['estoque'] ?>"
                        value="1"
                        required
                    >

                    <button
                        type="submit"
                        name="adicionar"
                        class="btn btn-aura w-100"
                    >
                        Adicionar ao carrinho
                    </button>

                </form>

            <?php else: ?>

                <p class="text-danger">
                    Produto indisponível.
                </p>

            <?php endif; ?>

            <a href="index.php"
               class="btn btn-outline-secondary mt-3">
                Voltar para a loja
            </a>

        </div>

    </div>

</div>

<footer>
    <p class="mb-0">
        Aura - Velas Aromáticas © 2026
    </p>
</footer>

</body>
</html>
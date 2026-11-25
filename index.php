<?php
session_start();
require_once "config/conexao.php";

$busca = $_GET['busca'] ?? '';
$categoria = $_GET['categoria'] ?? '';

$categorias = $conexao->query(
    "SELECT * FROM categorias"
)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM produtos WHERE 1=1";
$parametros = [];

if ($busca != '') {
    $sql .= " AND nome LIKE ?";
    $parametros[] = "%$busca%";
}

if ($categoria != '') {
    $sql .= " AND categoria_id = ?";
    $parametros[] = $categoria;
}

$consulta = $conexao->prepare($sql);
$consulta->execute($parametros);

$produtos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Aura - Velas Aromáticas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            Aura 
        </a>

        <div class="d-flex flex-wrap gap-3">
            <a class="nav-link" href="index.php">Início</a>

            <?php if (isset($_SESSION['usuario_id'])): ?>

                <a class="nav-link" href="logout.php">Sair</a>

                <?php if ($_SESSION['usuario_tipo'] == 'admin'): ?>
                    <a class="nav-link" href="admin/index.php">Admin</a>
                <?php endif; ?>

            <?php else: ?>

                <a class="nav-link" href="login.php">Entrar</a>
                <a class="nav-link" href="cadastro.php">Cadastro</a>

            <?php endif; ?>

            <a class="nav-link" href="carrinho.php">Carrinho</a>
        </div>
    </div>
</nav>

<section class="banner">
    <h1>Transforme momentos em sensações.</h1>
    <p>Aromas que acolhem e encantam.</p>
    <a href="#produtos" class="btn btn-aura">
        Conheça nossas velas
    </a>
</section>

<div class="container" id="produtos">

    <h2 class="text-center mb-4">Nossas velas</h2>

    <form method="GET" class="row g-3 mb-4">

        <div class="col-md-6">
            <input
                type="text"
                name="busca"
                class="form-control"
                placeholder="Pesquisar vela..."
                value="<?= htmlspecialchars($busca) ?>"
            >
        </div>

        <div class="col-md-4">
            <select name="categoria" class="form-select">

                <option value="">Todas as categorias</option>

                <?php foreach ($categorias as $cat): ?>

                    <option value="<?= $cat['id'] ?>"
                        <?= $categoria == $cat['id'] ? 'selected' : '' ?>>

                        <?= htmlspecialchars($cat['nome']) ?>

                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-aura w-100">
                Buscar
            </button>
        </div>

    </form>

    <div class="row g-4">

        <?php if (empty($produtos)): ?>

            <p class="text-center">
                Nenhum produto encontrado.
            </p>

        <?php endif; ?>

        <?php foreach ($produtos as $produto): ?>

            <div class="col-12 col-sm-6 col-md-3">

                <div class="card">

                    <img
                        src="img/<?= htmlspecialchars($produto['imagem']) ?>"
                        class="card-img-top"
                        alt="<?= htmlspecialchars($produto['nome']) ?>"
                    >

                    <div class="card-body text-center">

                        <h5 class="card-title">
                            <?= htmlspecialchars($produto['nome']) ?>
                        </h5>

                        <p class="preco">
                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                        </p>

                        <a
                            href="produto.php?id=<?= $produto['id'] ?>"
                            class="btn btn-aura w-100"
                        >
                            Ver detalhes
                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<footer>
    <p class="mb-0">
        Aura - Velas Aromáticas © 2026
    </p>
</footer>

</body>
</html>
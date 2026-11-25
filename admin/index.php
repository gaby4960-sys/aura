<?php
session_start();

if (($_SESSION['usuario_tipo'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin - Aura</title>

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
            <a class="nav-link" href="../index.php">Voltar à loja</a>
            <a class="nav-link" href="../logout.php">Sair</a>
        </div>

    </div>
</nav>

<div class="container mt-5 mb-5">

    <h2 class="text-center">Painel administrativo</h2>

    <p class="text-center mb-5">
        Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!
        Gerencie sua loja Aura.
    </p>

    <div class="row justify-content-center g-4">

        <div class="col-md-5">

            <div class="card p-4 text-center">

                <h2>Produtos</h2>

                <p>Cadastrar, editar e excluir velas.</p>

                <a href="produtos.php" class="btn btn-aura">
                    Gerenciar produtos
                </a>

            </div>

        </div>

        <div class="col-md-5">

            <div class="card p-4 text-center">

                <h2>Pedidos</h2>

                <p>Visualizar compras realizadas.</p>

                <a href="pedidos.php" class="btn btn-aura">
                    Visualizar pedidos
                </a>

            </div>

        </div>

    </div>

</div>

<footer>
    Aura - Velas Aromáticas © 2026
</footer>

</body>
</html>
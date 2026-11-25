<?php
session_start();
require_once "config/conexao.php";

$mensagem = "";

if (isset($_POST['entrar'])) {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $consulta = $conexao->prepare(
        "SELECT * FROM usuarios WHERE email = ?"
    );

    $consulta->execute([$email]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {

        session_regenerate_id(true);

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];

        header("Location: index.php");
        exit;

    } else {

        $mensagem = "E-mail ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Aura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<nav class="navbar">
    <div class="container">

        <a class="navbar-brand" href="index.php">Aura 🌷</a>

        <div class="d-flex gap-3">
            <a class="nav-link" href="index.php">Início</a>
            <a class="nav-link" href="cadastro.php">Cadastro</a>
        </div>

    </div>
</nav>

<div class="container">

    <div class="caixa-formulario">

        <h2 class="text-center">Entre na sua conta</h2>

        <p class="text-center mb-4">
            Que bom ter você aqui!
        </p>

        <?php if (($_GET['cadastro'] ?? '') == 'ok'): ?>

            <div class="alert alert-success">
                Cadastro realizado com sucesso!
            </div>

        <?php endif; ?>

        <?php if ($mensagem != ""): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($mensagem) ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <label class="form-label">E-mail</label>

            <input type="email"
                   name="email"
                   class="form-control mb-3"
                   placeholder="Digite seu e-mail"
                   required>

            <label class="form-label">Senha</label>

            <input type="password"
                   name="senha"
                   class="form-control mb-3"
                   placeholder="Digite sua senha"
                   required>

            <button type="submit"
                    name="entrar"
                    class="btn btn-aura w-100">
                Entrar
            </button>

        </form>

        <p class="text-center mt-3">
            Não possui uma conta?
            <a href="cadastro.php">Cadastre-se</a>
        </p>

    </div>

</div>

<footer>
    Aura - Velas Aromáticas © 2026
</footer>

</body>
</html>
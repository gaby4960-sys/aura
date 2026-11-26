<?php
session_start();
require_once "config/conexao.php";

$mensagem = "";

if (isset($_POST['cadastrar'])) {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $consulta = $conexao->prepare(
        "SELECT id FROM usuarios WHERE email = ?"
    );

    $consulta->execute([$email]);

    if ($consulta->fetch()) {

        $mensagem = "Este e-mail já está cadastrado!";

    } elseif (empty($nome) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($senha) < 6) {

        $mensagem = "Preencha os dados corretamente. A senha deve ter pelo menos 6 caracteres.";

    } else {

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios
                (nome, email, senha, tipo)
                VALUES (?, ?, ?, 'cliente')";

        $consulta = $conexao->prepare($sql);

        $consulta->execute([
            $nome,
            $email,
            $senhaHash
        ]);

        header("Location: login.php?cadastro=ok");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro - Aura</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="index.php">Aura 🌷</a>

        <div class="d-flex gap-3">
            <a class="nav-link" href="index.php">Início</a>
            <a class="nav-link" href="login.php">Entrar</a>
        </div>
    </div>
</nav>

<div class="container">

    <div class="caixa-formulario">

        <h2 class="text-center">Crie sua conta</h2>

        <p class="text-center mb-4">
            Bem-vinda à Aura!
        </p>

        <?php if ($mensagem != ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label class="form-label">Nome completo</label>

            <input type="text"
                   name="nome"
                   class="form-control mb-3"
                   placeholder="Digite seu nome"
                   required>

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
                   minlength="6"
                   placeholder="Digite sua senha"
                   required>

            <button type="submit"
                    name="cadastrar"
                    class="btn btn-aura w-100">
                Cadastrar
            </button>

        </form>

        <p class="text-center mt-3">
            Já possui uma conta?
            <a href="login.php">Entrar</a>
        </p>

    </div>

</div>

<footer>
    Aura - Velas Aromáticas © 2026
</footer>
<script src="script.js"></script>

</body>
</html>
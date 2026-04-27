<?php
// login.php
// FRAMEWORK: Bootstrap 5 (importado via CDN em layout_login.php)
session_start();

// Se já logado, redireciona direto
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = trim($_POST['senha']   ?? '');

    if ($usuario === '' || $senha === '') {
        $erro = 'Preencha todos os campos.';
    } else {
        // Verificar usuário e senha (MD5) no banco
        $stmt = $pdo->prepare("SELECT id, usuario FROM usuarios WHERE usuario = ? AND senha = MD5(?)");
        $stmt->execute([$usuario, $senha]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario']    = $user['usuario'];
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Usuário ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — To-Do List</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,.25);
        }
        .login-card .card-header {
            background: #0d6efd;
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
            text-align: center;
            color: #fff;
        }
        .login-card .card-header h4 { margin: 0; font-weight: 700; }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="card-header">
        <i class="bi bi-check2-square fs-2"></i>
        <h4 class="mt-2">To-Do List</h4>
        <small class="opacity-75">Sistema de Gerenciamento de Tarefas</small>
    </div>
    <div class="card-body p-4">

        <?php if ($erro !== ''): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span><?= htmlspecialchars($erro) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" novalidate>
            <div class="mb-3">
                <label for="usuario" class="form-label fw-semibold">
                    <i class="bi bi-person me-1"></i>Usuário
                </label>
                <input
                    type="text"
                    class="form-control form-control-lg"
                    id="usuario"
                    name="usuario"
                    placeholder="Digite seu usuário"
                    value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>"
                    required
                    autofocus
                >
            </div>

            <div class="mb-4">
                <label for="senha" class="form-label fw-semibold">
                    <i class="bi bi-lock me-1"></i>Senha
                </label>
                <input
                    type="password"
                    class="form-control form-control-lg"
                    id="senha"
                    name="senha"
                    placeholder="Digite sua senha"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>
        </form>

        <p class="text-center text-muted small mt-3 mb-0">
            Credenciais de teste: <strong>admin</strong> / <strong>123456</strong>
        </p>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

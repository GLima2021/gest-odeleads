<?php
session_start();

// Credenciais fixas
$usuario_autorizado = "USUARIO";
$senha_autorizada = "SENHA";

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $senha = $_POST["senha"] ?? '';

    if ($email === $usuario_autorizado && $senha === $senha_autorizada) {
        $_SESSION["logado"] = true;
        header("Location:admin_clicks.php"); // Renomeie seu dashboard para 	admin_clicks.php
        exit;
    } else {
        $erro = "Email ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Seguro</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #0d0d0d;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: #1c1c1c;
      padding: 30px 25px;
      border-radius: 16px;
      width: 100%;
      max-width: 380px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #ffffff;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border-radius: 10px;
      border: 1px solid #333;
      background-color: #2c2c2c;
      color: #fff;
      font-size: 16px;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color:rgb(255, 81, 0);
      background-color: #333;
    }

    .recaptcha {
      background-color: #121212;
      padding: 12px;
      border-radius: 8px;
      color: #bbb;
      font-size: 14px;
      margin-top: 12px;
      text-align: center;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg,rgb(255, 120, 17),rgb(248, 136, 24));
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background: linear-gradient(135deg, #a66bbe, #9b59b6);
    }

    .error {
      color: #ff4d4f;
      background-color: #2a1a1a;
      padding: 10px;
      text-align: center;
      border-radius: 8px;
      margin-bottom: 10px;
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 25px 20px;
      }

      h2 {
        font-size: 22px;
      }

      input, button {
        font-size: 15px;
      }
    }
  </style>
</head>
<body>
  <form method="POST" class="login-container">
    <h2>Login Seguro</h2>

    <?php if (isset($erro)): ?>
      <div class="error"><?= $erro ?></div>
    <?php endif; ?>

    <input type="email" name="email" placeholder="Digite seu email" required>
    <input type="password" name="senha" placeholder="Digite sua senha" required>

    <div class="recaptcha">✔️ Confirme que é humano (Cloudflare visual)</div>

    <button type="submit">Entrar</button>
  </form>
</body>
</html>

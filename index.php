<?php
session_start();

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificação de login
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        $cpf_cnpj = $_POST['login_cpf_cnpj'];
        $password = $_POST['login_password'];

        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE cpf_cnpj = ?');
        $stmt->execute([$cpf_cnpj]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['senha'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_telefone'] = $user['telefone'];
            header('Location: contato.php');
            exit();
        } else {
            $_SESSION['error_message'] = "CPF/CNPJ ou senha incorretos.";
            header("Location: index.php");
            exit();
        }
    }

    // Registro de usuário
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
        $cpf_cnpj = $_POST['cpf_cnpj'];
        $nome = $_POST['nome'];
        $sobrenome = $_POST['sobrenome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

        // Validações básicas
        if (strlen($cpf_cnpj) < 11 || strlen($senha) < 8) {
            $_SESSION['error_message'] = "CPF/CNPJ inválido ou senha muito curta.";
            header("Location: index.php");
            exit();
        }

        $stmt = $pdo->prepare('INSERT INTO usuarios (cpf_cnpj, nome, sobrenome, email, telefone, senha) VALUES (?, ?, ?, ?, ?, ?)');
        if ($stmt->execute([$cpf_cnpj, $nome, $sobrenome, $email, $telefone, $senha])) {
            $_SESSION['success_message'] = "Cadastro realizado com sucesso!";
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Erro ao cadastrar: " . $stmt->errorInfo()[2];
            header("Location: index.php");
            exit();
        }
    }
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login e Cadastro - Naldo Painéis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #212121;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            width: 350px;
            background-color: #333;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            animation: fadeIn 1s ease-in-out;
        }

        .header {
            background-color: #ff0000;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1.5px;
            animation: slideDown 1s ease-in-out;
        }

        .card {
            padding: 30px 20px;
        }

        h2 {
            color: #ffffff;
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            animation: slideRight 0.8s ease-in-out;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #555;
            border-radius: 8px;
            background-color: #444;
            color: #ffffff;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="tel"]:focus {
            border-color: #ff0000;
            outline: none;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.7);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #ff0000;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #cc0000;
        }

        .error-message {
            color: #ff0000;
            text-align: center;
            margin: 10px 0;
        }

        .success-message {
            color: #00ff00;
            text-align: center;
            margin: 10px 0;
        }

        .toggle-btn {
            width: 100%;
            background: transparent;
            border: none;
            color: #ff0000;
            cursor: pointer;
            text-decoration: underline;
            font-size: 16px;
            margin-top: 10px;
        }

        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes slideRight {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Naldo Painéis</div>
        <div id="loginForm" class="form-container active">
            <div class="card">
                <h2>Login</h2>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>
                <form action="index.php" method="POST">
                    <input type="text" name="login_cpf_cnpj" placeholder="CPF/CNPJ" required>
                    <input type="password" name="login_password" placeholder="Senha" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <button class="toggle-btn" onclick="toggleForm()">Cadastrar</button>
            </div>
        </div>

        <div id="registerForm" class="form-container">
            <div class="card">
                <h2>Cadastro</h2>
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="success-message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                <?php endif; ?>
                <form action="index.php" method="POST">
                    <input type="text" name="cpf_cnpj" placeholder="CPF/CNPJ" required>
                    <input type="text" name="nome" placeholder="Nome" required>
                    <input type="text" name="sobrenome" placeholder="Sobrenome" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="tel" name="telefone" placeholder="Telefone" required>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <button type="submit" name="register">Cadastrar</button>
                </form>
                <button class="toggle-btn" onclick="toggleForm()">Login</button>
            </div>
        </div>
    </div>

    <script>
        function toggleForm() {
            document.getElementById('loginForm').classList.toggle('active');
            document.getElementById('registerForm').classList.toggle('active');
        }
    </script>
</body>
</html>

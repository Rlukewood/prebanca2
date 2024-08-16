<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Iniciar sessão
session_start();

// Função para validar CPF
function validateCPF($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) != 11) return false;
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $sum += $cpf[$i] * (10 - $i);
    }
    $rest = ($sum * 10) % 11;
    $rest = ($rest == 10 || $rest == 11) ? 0 : $rest;
    if ($rest != $cpf[9]) return false;
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
        $sum += $cpf[$i] * (11 - $i);
    }
    $rest = ($sum * 10) % 11;
    $rest = ($rest == 10 || $rest == 11) ? 0 : $rest;
    if ($rest != $cpf[10]) return false;
    return true;
}

// Função para validar CNPJ
function validateCNPJ($cnpj) {
    $cnpj = preg_replace('/\D/', '', $cnpj);
    if (strlen($cnpj) != 14) return false;
    $sum = 0;
    $weight = 5;
    for ($i = 0; $i < 12; $i++) {
        $sum += $cnpj[$i] * $weight;
        $weight = ($weight == 2) ? 9 : $weight - 1;
    }
    $rest = $sum % 11;
    $rest = ($rest < 2) ? 0 : 11 - $rest;
    if ($rest != $cnpj[12]) return false;
    $sum = 0;
    $weight = 6;
    for ($i = 0; $i < 13; $i++) {
        $sum += $cnpj[$i] * $weight;
        $weight = ($weight == 2) ? 9 : $weight - 1;
    }
    $rest = $sum % 11;
    $rest = ($rest < 2) ? 0 : 11 - $rest;
    if ($rest != $cnpj[13]) return false;
    return true;
}

// Processar o formulário
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Cadastro
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpfCnpj = $_POST['cpfCnpj'];

        // Validação
        if (!validateCPF($cpfCnpj) && !validateCNPJ($cpfCnpj)) {
            $errors[] = 'CPF ou CNPJ inválido';
        }
        if (strlen($password) < 8) {
            $errors[] = 'A senha deve ter pelo menos 8 caracteres';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('INSERT INTO users (name, surname, phone, email, password, cpfCnpj) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $surname, $phone, $email, password_hash($password, PASSWORD_BCRYPT), $cpfCnpj]);
            echo "<script>alert('Cadastro realizado com sucesso'); document.getElementById('signup_toggle').checked = false;</script>";
        }
    } elseif (isset($_POST['login'])) {
        // Login
        $email = $_POST['login_email'];
        $password = $_POST['login_password'];
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            header('Location: https://www.instagram.com/naldopaineis/?hl=en');
            exit();
        } else {
            $errors[] = 'Email ou senha inválidos';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro e Login</title>
    <style>
        body { margin: 0; padding: 0; height: 100vh; background-color: #333; display: flex; justify-content: center; align-items: center; }
        .container { display: flex; justify-content: center; align-items: center; position: relative; width: 300px; height: 400px; }
        .form { display: flex; justify-content: center; align-items: center; position: relative; width: 100%; height: 100%; transform-style: preserve-3d; transition: all 1s ease; }
        .form .form_front, .form .form_back { display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 20px; position: absolute; backface-visibility: hidden; padding: 65px 45px; border-radius: 15px; box-shadow: inset 2px 2px 10px rgba(0,0,0,1), inset -1px -1px 5px rgba(255, 255, 255, 0.6); background-color: #212121; color: #fff; }
        .form .form_back { transform: rotateY(-180deg); }
        .form_details { font-size: 25px; font-weight: 600; padding-bottom: 10px; color: white; }
        .input { width: 245px; min-height: 45px; color: #fff; outline: none; transition: 0.35s; padding: 0px 7px; background-color: #212121; border-radius: 6px; border: 2px solid #212121; box-shadow: 6px 6px 10px rgba(0,0,0,1), 1px 1px 10px rgba(255, 255, 255, 0.6); }
        .input::placeholder { color: #999; }
        .input:focus { transform: scale(1.05); box-shadow: 6px 6px 10px rgba(0,0,0,1), 1px 1px 10px rgba(255, 255, 255, 0.6), inset 2px 2px 10px rgba(0,0,0,1), inset -1px -1px 5px rgba(255, 255, 255, 0.6); }
        .btn { padding: 10px 35px; cursor: pointer; background-color: #e53935; border-radius: 6px; border: 2px solid #e53935; box-shadow: 6px 6px 10px rgba(0,0,0,1), 1px 1px 10px rgba(255, 255, 255, 0.6); color: #fff; font-size: 15px; font-weight: bold; transition: 0.35s; }
        .btn:hover { transform: scale(1.05); box-shadow: 6px 6px 10px rgba(0,0,0,1), 1px 1px 10px rgba(255, 255, 255, 0.6), inset 2px 2px 10px rgba(0,0,0,1), inset -1px -1px 5px rgba(255, 255, 255, 0.6); }
        .form .switch { font-size: 13px; color: white; }
        .form .switch .signup_tog { font-weight: 700; cursor: pointer; text-decoration: underline; }
        .form .error { color: red; font-size: 12px; margin-top: 10px; }
        .form .error-container { position: absolute; bottom: 10px; width: 100%; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <input type="checkbox" id="signup_toggle" style="display: none;">
        <div class="form">
            <!-- Formulário de Cadastro -->
            <div class="form_front">
                <div class="form_details">Cadastro</div>
                <form method="post">
                    <input class="input" type="text" name="name" placeholder="Nome" required>
                    <input class="input" type="text" name="surname" placeholder="Sobrenome" required>
                    <input class="input" type="text" name="phone" placeholder="Telefone" required>
                    <input class="input" type="text" name="cpfCnpj" placeholder="CPF ou CNPJ" required>
                    <input class="input" type="email" name="email" placeholder="Email" required>
                    <input class="input" type="password" name="password" placeholder="Senha" required>
                    <button class="btn" type="submit" name="register">Cadastrar</button>
                </form>
                <div class="switch">Já possui uma conta? <label for="signup_toggle" class="signup_tog">Login</label></div>
                <div class="error-container">
                    <?php if (!empty($errors)) { foreach ($errors as $error) { echo "<div class='error'>$error</div>"; } } ?>
                </div>
            </div>
            <!-- Formulário de Login -->
            <div class="form_back">
                <div class="form_details">Naldo Login</div>
                <form method="post">
                    <input class="input" type="email" name="login_email" placeholder="Email" required>
                    <input class="input" type="password" name="login_password" placeholder="Senha" required>
                    <button class="btn" type="submit" name="login">Login</button>
                </form>
                <div class="switch">Não tem uma conta? <label for="signup_toggle" class="signup_tog">Cadastro</label></div>
                <div class="error-container">
                    <?php if (!empty($errors)) { foreach ($errors as $error) { echo "<div class='error'>$error</div>"; } } ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('signup_toggle').addEventListener('change', function() {
            document.querySelector('.form').style.transform = this.checked ? 'rotateY(-180deg)' : 'rotateY(0deg)';
        });
    </script>
</body>
</html>

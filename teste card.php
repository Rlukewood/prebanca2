
<?php
// Funções de validação
function validarCPF($cpf) {
    // Remover caracteres não numéricos
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verificar se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) return false;

    // Verificar se o CPF é uma sequência de números iguais
    if (preg_match('/(\d)\1{10}/', $cpf)) return false;

    // Validar CPF
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) return false;
    }

    return true;
}

function validarCNPJ($cnpj) {
    // Remover caracteres não numéricos
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    // Verificar se o CNPJ tem 14 dígitos
    if (strlen($cnpj) != 14) return false;

    // Validar CNPJ
    for ($t = 12; $t < 14; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cnpj[$c] * ($t - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cnpj[$c] != $d) return false;
    }

    return true;
}

function validarTelefone($telefone) {
    // Remover caracteres não numéricos
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    // Verificar se o telefone tem 10 ou 11 dígitos
    return (strlen($telefone) == 10 || strlen($telefone) == 11);
}

// Configurações do banco de dados
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'novo_banco';

// Cria a conexão
$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

$modo = 'login';
$mensagemErro = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['modo'])) {
        $modo = $_POST['modo'];
    }

    if ($modo == 'cadastro') {
        // Validação e inserção de dados
        if (isset($_POST['cpf_cnpj'], $_POST['nome'], $_POST['sobrenome'], $_POST['email'], $_POST['telefone'], $_POST['senha'])) {
            $cpf_cnpj = $conexao->real_escape_string($_POST['cpf_cnpj']);
            $nome = $conexao->real_escape_string($_POST['nome']);
            $sobrenome = $conexao->real_escape_string($_POST['sobrenome']);
            $email = $conexao->real_escape_string($_POST['email']);
            $telefone = $conexao->real_escape_string($_POST['telefone']);
            $senha = password_hash($conexao->real_escape_string($_POST['senha']), PASSWORD_DEFAULT);

            // Verificar CPF/CNPJ e telefone
            if (validarCPF($cpf_cnpj) || validarCNPJ($cpf_cnpj)) {
                if (validarTelefone($telefone)) {
                    // Verificar se o email já está cadastrado
                    $sqlCheck = "SELECT * FROM usuarios WHERE email = '$email'";
                    $resultCheck = $conexao->query($sqlCheck);

                    if ($resultCheck->num_rows > 0) {
                        $mensagemErro = "O email já está cadastrado. Por favor, use outro email.";
                    } else {
                        // Insere os dados na tabela usuarios
                        $sql = "INSERT INTO usuarios (cpf_cnpj, nome, sobrenome, email, telefone, senha) VALUES ('$cpf_cnpj', '$nome', '$sobrenome', '$email', '$telefone', '$senha')";

                        if ($conexao->query($sql) === TRUE) {
                            echo "Cadastro realizado com sucesso!";
                            $modo = 'login';
                        } else {
                            $mensagemErro = "Erro ao cadastrar: " . $conexao->error;
                        }
                    }
                } else {
                    $mensagemErro = "Telefone inválido.";
                }
            } else {
                $mensagemErro = "CPF ou CNPJ inválido.";
            }
        } else {
            $mensagemErro = "Por favor, preencha todos os campos.";
        }
    } elseif ($modo == 'login') {
        // Validação de login
        if (isset($_POST['email_login'], $_POST['senha_login'])) {
            $email = $conexao->real_escape_string($_POST['email_login']);
            $senha = $_POST['senha_login'];

            // Consulta o banco de dados para verificar as credenciais
            $sql = "SELECT * FROM usuarios WHERE email = '$email'";
            $result = $conexao->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                // Verifica a senha
                if (password_verify($senha, $row['senha'])) {
                    echo "Login bem-sucedido! <a href='link_protegido.php'>Clique aqui para acessar o link protegido</a>";
                    exit;
                } else {
                    $mensagemErro = "Senha incorreta.";
                }
            } else {
                $mensagemErro = "Não há esse login.";
            }
        } else {
            $mensagemErro = "Por favor, preencha todos os campos.";
        }
    }
}

// Fecha a conexão
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login e Cadastro</title>
    <link rel="stylesheet" href=".css"> <!-- Vinculação do CSS externo -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff; /* Cor 1: branco */
            color: #000; /* Cor 3: preto para letras */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header {
            background-color: #ff0000; /* Cor 2: vermelho */
            color: #000; /* Cor 3: preto para letras */
            padding: 10px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            top: 0;
            width: 100%;
            border-bottom: 2px solid #000; /* Preto para a borda inferior */
        }

        footer {
            background-color: #ff0000; /* Cor 2: vermelho */
            color: #000; /* Cor 3: preto para letras */
            padding: 10px;
            text-align: center;
            font-size: 14px;
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 2px solid #000; /* Preto para a borda superior */
        }


        .mensagem-erro {
            color: red;
            text-align: center;
            margin-bottom: 16px;
        }

        .alternar {
            text-align: center;
            margin: 16px 0;
        }

        .alternar button {
            background-color: #ff0000; /* Cor 1: vermelho */
            color: #fff; /* Cor 2: branco */
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .alternar button:hover {
            background-color: #cc0000; /* Cor 1: vermelho mais escuro */
        }
    </style>
</head>
<body>
    <header>
        Naldo Painéis
    </header>

    <div class="container">
        <h1><?php echo $modo == 'login' ? 'Naldo Login' : 'Cadastro'; ?></h1>
        <?php if (!empty($mensagemErro)): ?>
            <div class="mensagem-erro"><?php echo $mensagemErro; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <?php if ($modo == 'cadastro'): ?>
                <input type="hidden" name="modo" value="cadastro">
                <label for="cpf_cnpj">CPF ou CNPJ:</label>
                <input type="text" id="cpf_cnpj" name="cpf_cnpj" required><br><br>

                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required><br><br>

                <label for="sobrenome">Sobrenome:</label>
                <input type="text" id="sobrenome" name="sobrenome" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" required><br><br>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required><br><br>

                <input type="submit" value="Cadastrar">
            <?php else: ?>
                <input type="hidden" name="modo" value="login">
                <label for="email_login">Email:</label>
                <input type="email" id="email_login" name="email_login" required><br><br>

                <label for="senha_login">Senha:</label>
                <input type="password" id="senha_login" name="senha_login" required><br><br>

                <input type="submit" value="Login">
            <?php endif; ?>
        </form>
        <div class="alternar">
            <button onclick="toggleMode()">Mudar para <?php echo $modo == 'login' ? 'Cadastro' : 'Login'; ?></button>
        </div>
    </div>

    <footer>
        &copy; 2024 Naldo Painéis. Todos os direitos reservados.
    </footer>

    <script>
        function toggleMode() {
            document.querySelector('input[name="modo"]').value = '<?php echo $modo == 'login' ? 'cadastro' : 'login'; ?>';
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>

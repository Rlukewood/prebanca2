<?php
// Configurações do banco de dados
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "novobanco";

// Cria a conexão

$conexao = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Verifica a conexão
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos nome, email e senha estão definidos
    if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['senha'])) {
        $nome = $conexao->real_escape_string($_POST['nome']);
        $email = $conexao->real_escape_string($_POST['email']);
        $senha = password_hash($conexao->real_escape_string($_POST['senha']), PASSWORD_DEFAULT);

        // Insere os dados na tabela usuarios
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

        if ($conexao->query($sql) === TRUE) {
            echo "Registro inserido com sucesso!";
        } else {
            echo "Erro ao inserir registro: " . $conexao->error;
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}

// Fecha a conexão
$conexao->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
</head>
<body>
    <form method="post" action="">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>

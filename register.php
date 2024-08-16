<?php
session_start();

// Configurações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios_db";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Função para validar CPF
function validaCPF($cpf) {
    // Remover máscara
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Verificar se o CPF possui 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verificar números repetidos
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Calcular dígitos verificadores
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

// Função para validar CNPJ
function validaCNPJ($cnpj) {
    // Remover máscara
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
    // Verificar se o CNPJ possui 14 dígitos
    if (strlen($cnpj) != 14) {
        return false;
    }

    // Calcular dígitos verificadores
    $tamanho = strlen($cnpj) - 2;
    $numeros = substr($cnpj, 0, $tamanho);
    $digitos = substr($cnpj, $tamanho);
    $soma = 0;
    $pos = $tamanho - 7;
    for ($i = $tamanho; $i >= 1; $i--) {
        $soma += $numeros[$tamanho - $i] * $pos--;
        if ($pos < 2) {
            $pos = 9;
        }
    }
    $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
    if ($resultado != $digitos[0]) {
        return false;
    }
    $tamanho++;
    $numeros = substr($cnpj, 0, $tamanho);
    $soma = 0;
    $pos = $tamanho - 7;
    for ($i = $tamanho; $i >= 1; $i--) {
        $soma += $numeros[$tamanho - $i] * $pos--;
        if ($pos < 2) {
            $pos = 9;
        }
    }
    $resultado = $soma % 11 < 2 ? 0 : 11 - $soma % 11;
    return $resultado == $digitos[1];
}

// Função para validar Telefone
function validaTelefone($telefone) {
    // Remover máscara
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    
    // Verificar se o telefone possui 10 ou 11 dígitos (com ou sem DDD)
    return (strlen($telefone) == 10 || strlen($telefone) == 11);
}

// Receber dados do formulário
$cpf_cnpj = $_POST['cpf_cnpj'];
$nome = $_POST['nome'];
$sobrenome = $_POST['sobrenome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = $_POST['senha'];

// Verificar CPF/CNPJ
if (strlen($cpf_cnpj) == 11 && !validaCPF($cpf_cnpj)) {
    $_SESSION['error_message'] = "CPF inválido.";
    header("Location: index.php");
    exit();
} elseif (strlen($cpf_cnpj) == 14 && !validaCNPJ($cpf_cnpj)) {
    $_SESSION['error_message'] = "CNPJ inválido.";
    header("Location: index.php");
    exit();
} elseif (strlen($cpf_cnpj) != 11 && strlen($cpf_cnpj) != 14) {
    $_SESSION['error_message'] = "CPF/CNPJ deve ter 11 ou 14 dígitos.";
    header("Location: index.php");
    exit();
}

// Verificar telefone
if (!validaTelefone($telefone)) {
    $_SESSION['error_message'] = "Telefone inválido. Deve conter 10 ou 11 dígitos.";
    header("Location: index.php");
    exit();
}

// Verificar senha (mínimo 8 caracteres)
if (strlen($senha) < 8) {
    $_SESSION['error_message'] = "A senha deve ter no mínimo 8 caracteres.";
    header("Location: index.php");
    exit();
}

// Inserir dados na tabela
$senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
$sql = "INSERT INTO usuarios (cpf_cnpj, nome, sobrenome, email, telefone, senha) VALUES ('$cpf_cnpj', '$nome', '$sobrenome', '$email', '$telefone', '$senha_hashed')";

if ($conn->query($sql) === TRUE) {
    $_SESSION['success_message'] = "Cadastro realizado com sucesso!";
    header("Location: index.php");
    exit();
} else {
    $_SESSION['error_message'] = "Erro ao cadastrar: " . $conn->error;
    header("Location: index.php");
    exit();
}

$conn->close();
?>
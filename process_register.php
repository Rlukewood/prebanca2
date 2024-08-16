<?php
session_start();
include 'db_connection.php';

function validarCPF_CNPJ($cpf_cnpj) {
    // Lógica de validação de CPF/CNPJ (exemplo básico)
    return preg_match("/^[0-9]{11}$|^[0-9]{14}$/", $cpf_cnpj);
}

$cpf_cnpj = $_POST['cpf_cnpj'];
$nome = $_POST['nome'];
$sobrenome = $_POST['sobrenome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

if (!validarCPF_CNPJ($cpf_cnpj)) {
    $_SESSION['error_message'] = "CPF/CNPJ inválido.";
    header("Location: index.php");
    exit();
}

if (strlen($telefone) < 10) {
    $_SESSION['error_message'] = "Telefone inválido.";
    header("Location: index.php");
    exit();
}

$sql = "INSERT INTO usuarios (cpf_cnpj, nome, sobrenome, email, telefone, senha) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([$cpf_cnpj, $nome, $sobrenome, $email, $telefone, $senha]);
    $_SESSION['success_message'] = "Cadastro realizado com sucesso!";
    header("Location: index.php");
    exit();
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Erro ao cadastrar: " . $e->getMessage();
    header("Location: index.php");
    exit();
}
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        $email = $_POST['login_email'];
        $password = $_POST['login_password'];

        // Verificar o usuário no banco de dados
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Se o usuário for encontrado e a senha estiver correta
        if ($user && password_verify($password, $user['senha'])) {
            // Armazena os dados do usuário na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];

            // Debugging: Confirmação da sessão
            echo "Debug: Sessão iniciada com sucesso. Redirecionando para contato.php";

            // Redireciona para a página de contato
            header('Location: contato.php');
            exit();
        } else {
            $_SESSION['error_message'] = "Email ou senha incorretos.";
            header("Location: index.php");
            exit();
        }
    }
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>

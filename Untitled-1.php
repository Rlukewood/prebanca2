// Conecte-se ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Adicionar imagens
for ($i = 1; $i <= 4; $i++) {
    $imagemNome = "imagens/imagem" . $i . ".jpg";
    $sql = "INSERT INTO imagens (imagem) VALUES ('$imagemNome')";
    if ($conn->query($sql) === TRUE) {
        echo "Imagem $i adicionada com sucesso.<br>";
    } else {
        echo "Erro ao adicionar imagem $i: " . $conn->error . "<br>";
    }
}

$conn->close();

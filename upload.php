<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Imagens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            margin: 0 0 20px;
            text-align: center;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Upload de Imagem</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="imagem" accept="image/*" required>
            <input type="submit" name="submit" value="Enviar">
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        // Configurações do banco de dados
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "usuarios_db";

        // Conectar ao banco de dados
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Verificar conexão
        if ($conn->connect_error) {
            die("Conexão falhou: " . $conn->connect_error);
        }

        // Verificar se o arquivo foi enviado
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $imagem = file_get_contents($_FILES['imagem']['tmp_name']);

            // Consultar o número de imagens existentes
            $sql = "SELECT COUNT(*) AS total FROM imagens";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $total_imagens = $row['total'];

            // Gerar o novo nome da imagem
            $novo_nome = "imagem" . ($total_imagens + 1) . ".jpg";

            // Preparar e executar a consulta para inserir a imagem
            $stmt = $conn->prepare("INSERT INTO imagens (nome, imagem) VALUES (?, ?)");
            $stmt->bind_param("sb", $novo_nome, $imagem);
            $stmt->send_long_data(1, $imagem);

            if ($stmt->execute()) {
                echo "<p>Imagem enviada com sucesso como '$novo_nome'!</p>";
            } else {
                echo "<p>Erro ao enviar a imagem: " . $stmt->error . "</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Erro no upload do arquivo.</p>";
        }

        $conn->close();
    }
    ?>
</body>
</html>

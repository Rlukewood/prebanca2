<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exibir Imagens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .gallery-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
            padding: 10px;
            background-color: #fff;
        }
        .gallery img {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        .gallery button {
            margin-top: 10px;
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .gallery button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h1>Galeria de Imagens</h1>
    <div class="gallery">
        <?php
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

        // Excluir imagem se o botão for clicado
        if (isset($_GET['delete'])) {
            $id = intval($_GET['delete']);
            $delete_sql = "DELETE FROM imagens WHERE id=?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo "<p>Imagem excluída com sucesso!</p>";
            } else {
                echo "<p>Erro ao excluir a imagem: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        // Consultar as imagens
        $sql = "SELECT id, nome, imagem FROM imagens";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Exibir cada imagem
            while ($row = $result->fetch_assoc()) {
                $imagem = 'data:image/jpeg;base64,' . base64_encode($row['imagem']);
                echo '<div class="gallery-item">';
                echo '<p>' . htmlspecialchars($row['nome']) . '</p>';
                echo "<img src='$imagem' alt='" . htmlspecialchars($row['nome']) . "'>";
                echo '<a href="?delete=' . $row['id'] . '" onclick="return confirm(\'Tem certeza que deseja excluir esta imagem?\');"><button>Excluir</button></a>';
                echo '</div>';
            }
        } else {
            echo "<p>Nenhuma imagem encontrada.</p>";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>

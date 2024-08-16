<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_nome'])) {
    header("Location: index.php");
    exit();
}

// Dados do usuário
$nome = isset($_SESSION['user_nome']) ? $_SESSION['user_nome'] : 'Usuário';
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'email@exemplo.com';
$telefone = isset($_SESSION['user_telefone']) ? $_SESSION['user_telefone'] : '0000-0000';

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Buscar imagens aleatoriamente
$sql = "SELECT imagem FROM imagens ORDER BY RAND() LIMIT 4";
$result = $conn->query($sql);

$imagens = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Converta a imagem para base64
        $imagens[] = 'data:image/jpeg;base64,' . base64_encode($row['imagem']);
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Naldo Painéis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
            overflow: hidden;
        }
        header {
            background-color: #ff0000;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-weight: bold;
            font-size: 24px;
            z-index: 2; /* Higher than carrossel but lower than card */
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        header a {
            color: #fff;
            text-decoration: none;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px;
            background-color: #000;
            color: #fff;
            font-size: 14px;
            z-index: 2; /* Higher than carrossel but lower than card */
        }
        .carrossel {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1; /* Behind the card and header */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #333; /* Fundo cinza escuro */
        }
        .carrossel img {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .carrossel img.active {
            opacity: 1;
        }
        .container {
            background-color: #595959;
            padding: 20px;
            border-radius: 12px;
            max-width: 80%; /* Largura menor que o cabeçalho */
            margin: 80px auto 0; /* Ajustado para ficar logo abaixo do cabeçalho */
            text-align: center;
            z-index: 2; /* Above the carrossel */
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: #fff;
            font-size: 20px;
            margin: 0;
        }
        #orcamentoCard {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #ffffff;
            max-width: 90%;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 3; /* Higher than carrossel and header */
            overflow: hidden;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        #orcamentoCard.show {
            display: block;
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        #orcamentoCard.hide {
            display: none;
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.8);
        }
        #orcamentoCard .card-info {
            background: #ffffff;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: center;
        }
        .form-group label {
            display: block;
            color: #666;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .button {
            padding: 10px 20px;
            font-weight: bold;
            background-color: #ff0000;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            width: 100%; /* Same width for all buttons */
            max-width: 300px; /* Adjust if needed */
            text-align: center; /* Center text inside buttons */
        }
        .button:hover {
            background-color: #cc0000;
        }
        .toggle-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            font-weight: bold;
            background-color: #ff0000;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            z-index: 4; /* Higher than card and carrossel */
            width: 100%; /* Same width for all buttons */
            max-width: 300px; /* Adjust if needed */
            text-align: center; /* Center text inside buttons */
        }
        .toggle-button:hover {
            background-color: #cc0000;
        }
        .instagram-button {
            padding: 10px 20px;
            font-weight: bold;
            background-color: #ff0000;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 10px;
            width: 100%; /* Same width for all buttons */
            max-width: 300px; /* Adjust if needed */
            text-align: center; /* Center text inside buttons */
        }
        .instagram-button:hover {
            background-color: #cc0000;
        }
        @media (max-width: 768px) {
            #orcamentoCard {
                width: 90%;
                height: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="https://www.naldopaineis.com.br">Naldo Painéis</a>
    </header>

    <div class="carrossel">
        <?php foreach($imagens as $index => $imagem): ?>
            <img src="<?php echo htmlspecialchars($imagem); ?>" alt="Imagem do Carrossel" class="<?php echo $index === 0 ? 'active' : ''; ?>">
        <?php endforeach; ?>
    </div>

    <div class="container">
        <h1>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h1>
    </div>

    <button class="toggle-button" onclick="toggleCard()">
        Solicitar Orçamento
    </button>

    <div id="orcamentoCard" class="hide">
        <div class="card-info">
            <p class="title">Solicitar Orçamento</p>
            <form action="enviar_email.php" method="post">
                <div class="form-group">
                    <label for="nome">Seu Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Seu E-mail:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefone">Seu Telefone:</label>
                    <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($telefone); ?>" required>
                </div>
                <div class="form-group">
                    <label for="assunto">Assunto:</label>
                    <textarea id="assunto" name="assunto" rows="4" required></textarea>
                </div>
                <button type="submit" class="button">Enviar E-mail</button>
                <button class="button" onclick="window.location.href='tel:+5545998408629'">
                    Ligar para (45) 99840-8629
                </button>
                <button class="instagram-button" onclick="window.location.href='https://www.instagram.com/naldopaineis/?hl=en'">
                    Instagram
                </button>
            </form>
        </div>
    </div>

    <footer>
        Contato e Localização: Rua Cristóvão Colombo, 1097 - Pioneiros Catarinenses, Cascavel - PR, 85805-510
    </footer>

    <script>
        function toggleCard() {
            var card = document.getElementById('orcamentoCard');
            if (card.classList.contains('hide')) {
                card.classList.remove('hide');
                card.classList.add('show');
            } else {
                card.classList.remove('show');
                card.classList.add('hide');
            }
        }

        // JavaScript para mudar a imagem de fundo a cada 5 segundos
        const images = document.querySelectorAll('.carrossel img');
        let currentIndex = 0;

        function changeImage() {
            images.forEach((img, index) => {
                img.classList.remove('active');
                if (index === currentIndex) {
                    img.classList.add('active');
                }
            });
            currentIndex = (currentIndex + 1) % images.length;
        }

        setInterval(changeImage, 5000); // Mudar imagem a cada 5 segundos
    </script>
</body>
</html>

<?php
session_start();
include '../db.php';

// Função para registar logs
if (!function_exists('registarLog')) {
    function registarLog($conn, $id_utilizador, $acao, $descricao) {
        $sql = "INSERT INTO logs (id_utilizador, acao, descricao) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Erro na preparação da query de log: " . $conn->error);
        }

        $stmt->bind_param("iss", $id_utilizador, $acao, $descricao);

        if (!$stmt->execute()) {
            error_log("Erro ao registar log: " . $stmt->error);
        }

        $stmt->close();
    }
}

// Verifica se o utilizador está logado e é administrador
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Obtém o ID do utilizador logado
$id_utilizador = $_SESSION['user_id'];

// Verifica ações enviadas pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'eliminar') {
            $id_carro = intval($_POST['id_carro']);
            $sql = "DELETE FROM carros WHERE id_carro = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_carro);

            if ($stmt->execute()) {
                $descricao = "Veículo com ID $id_carro eliminado.";
                registarLog($conn, $id_utilizador, 'Eliminar', $descricao);
            } else {
                error_log("Erro ao eliminar o veículo: " . $stmt->error);
            }

            $stmt->close();
        } elseif ($action === 'adicionar') {
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $ano = intval($_POST['ano']);
            $preco_dia = floatval($_POST['preco_dia']);
            $imagem = $_POST['imagem'];

            $sql = "INSERT INTO carros (marca, modelo, ano, preco_dia, imagem) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssids", $marca, $modelo, $ano, $preco_dia, $imagem);

            if ($stmt->execute()) {
                $descricao = "Novo veículo adicionado: Marca: $marca, Modelo: $modelo, Ano: $ano, Preço/Dia: $preco_dia.";
                registarLog($conn, $id_utilizador, 'Adicionar', $descricao);
            } else {
                error_log("Erro ao adicionar o veículo: " . $stmt->error);
            }

            $stmt->close();
        } elseif ($action === 'editar') {
            $id_carro = intval($_POST['id_carro']);
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $ano = intval($_POST['ano']);
            $preco_dia = floatval($_POST['preco_dia']);

            // Buscar imagem atual
            $sql_img = "SELECT imagem FROM carros WHERE id_carro = ?";
            $stmt_img = $conn->prepare($sql_img);
            $stmt_img->bind_param("i", $id_carro);
            $stmt_img->execute();
            $stmt_img->bind_result($imagem_atual);
            $stmt_img->fetch();
            $stmt_img->close();

            // Verifica se foi feito upload de nova imagem
            $imagem = $imagem_atual; // padrão
            if (isset($_FILES['imagem_nova']) && $_FILES['imagem_nova']['error'] === 0) {
                $nome_novo = time() . "_" . basename($_FILES['imagem_nova']['name']);
                $caminho = "uploads/" . $nome_novo;

                // Criar diretório se não existir
                if (!is_dir("uploads")) {
                    mkdir("uploads", 0755, true);
                }

                if (move_uploaded_file($_FILES['imagem_nova']['tmp_name'], $caminho)) {
                    $imagem = $caminho;
                }
            }

            $sql = "UPDATE carros SET marca = ?, modelo = ?, ano = ?, preco_dia = ?, imagem = ? WHERE id_carro = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssidsi", $marca, $modelo, $ano, $preco_dia, $imagem, $id_carro);

            if ($stmt->execute()) {
                $descricao = "Veículo com ID $id_carro atualizado: Marca: $marca, Modelo: $modelo, Ano: $ano, Preço/Dia: $preco_dia.";
                registarLog($conn, $id_utilizador, 'Editar', $descricao);
            } else {
                error_log("Erro ao atualizar o veículo: " . $stmt->error);
            }

            $stmt->close();
        }

    }
}

// Consulta os veículos
$sql = "SELECT id_carro, marca, modelo, ano, preco_dia, imagem FROM carros";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Registos de Ações</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./css/addveiculos.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
    <aside class="sidebar">
        <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="utilizadores.php"><i class="fas fa-users"></i> Utilizadores</a></li>
                <li><a href="condutores.php"><i class="fas fa-id-card"></i> Condutores</a></li>
                <li><a href="veiculos.php"><i class="fas fa-car"></i> Veículos</a></li>
                <li><a href="reservas.php"><i class="fas fa-book"></i> Reservas</a></li>
                <li><a href="admin_chat.php"><i class="fas fa-phone"></i> Chat</a></li>


                <!-- Mostrar apenas para ChiefAdmin -->
                <?php if (isset($_SESSION['user_permission']) && $_SESSION['user_permission'] === 'chiefadmin'): ?>
                    <li><a href="resets.php"><i class="fas fa-lock"></i> Password Resets</a></li>
                    <li><a href="logs.php"><i class="fas fa-cogs"></i> Logs</a></li>
                <?php endif; ?>

                <li><a href="../logout.php"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
    </aside>

        <main class="dashboard">
            <div class="title-bar">
                <h1>Veículos</h1>
                <div class="add-car-btn">
                    <a href="addveiculos.php">Adicionar Carro</a>
                </div>
            </div>


            <table>
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Ano</th>
                        <th>Preço/Dia</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <form method="post" enctype="multipart/form-data">
                            <td><input type="text" name="marca" value="<?php echo htmlspecialchars($row['marca']); ?>"></td>
                            <td><input type="text" name="modelo" value="<?php echo htmlspecialchars($row['modelo']); ?>"></td>
                            <td><input type="number" name="ano" value="<?php echo htmlspecialchars($row['ano']); ?>"></td>
                            <td><input type="number" step="0.01" name="preco_dia" value="<?php echo htmlspecialchars($row['preco_dia']); ?>"></td>
                            <td style="text-align: center;">
                                <div class="image-upload-container" onclick="document.getElementById('input_<?php echo $row['id_carro']; ?>').click();">
                                    <img id="preview_<?php echo $row['id_carro']; ?>"
                                src="<?php echo htmlspecialchars($row['imagem']); ?>"
                                alt="Imagem atual"
                                class="preview-img">


                                    <div class="overlay">Editar</div>
                                </div>

                                <input type="file"
                                    id="input_<?php echo $row['id_carro']; ?>"
                                    name="imagem_nova"
                                    accept="image/*"
                                    onchange="mostrarPreview(this, 'preview_<?php echo $row['id_carro']; ?>')"
                                    style="display: none;">
                            </td>
                            <td>
                                <input type="hidden" name="id_carro" value="<?php echo $row['id_carro']; ?>">
                                <button type="submit" name="action" value="editar">Guardar</button>
                                <button type="submit" name="action" value="eliminar">Eliminar</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script>
function mostrarPreview(input, idPreview) {
    const file = input.files[0];
    const preview = document.getElementById(idPreview);

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>

</body>
</html>

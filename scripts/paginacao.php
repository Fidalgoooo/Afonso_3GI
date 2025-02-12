<?php
include '../db.php';

// Verificar se o utilizador está logado e tem permissão de administrador
if (!isset($_SESSION['user_permission']) || ($_SESSION['user_permission'] !== 'adm' && $_SESSION['user_permission'] !== 'chiefadmin')) {
    header("Location: ../login.php");
    exit;
}

// Definir a tabela dinamicamente a partir do GET
$tabela = 'utilizadores'; // Tabela fixa para esta página

// Número de registros por página
$registros_por_pagina = 10;

// Obter a página atual
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Contar total de registros na tabela
$sql_total = "SELECT COUNT(*) AS total FROM $tabela";
$total_resultado = $conn->query($sql_total);
$total_registros = $total_resultado->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Obter os registros da tabela com paginação
$sql = "SELECT id_utilizador, nome, email, permissao FROM $tabela LIMIT $registros_por_pagina OFFSET $offset";
$result = $conn->query($sql);

// Retornar os resultados para a página principal
return [$result, $total_paginas, $pagina_atual];

?>

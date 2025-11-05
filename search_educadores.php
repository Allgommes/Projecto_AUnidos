<?php
// search_educadores.php
require_once 'config/database.php';
header('Content-Type: application/json');

$distrito = $_GET['distrito'] ?? null;
$especialidade = $_GET['especialidade'] ?? null; // id
$preco_min = $_GET['preco_min'] ?? null;
$preco_max = $_GET['preco_max'] ?? null;

$sql = "SELECT e.id AS educador_id, u.nome, u.distrito, e.anos_experiencia, e.biografia, e.preco_minimo, e.preco_maximo, e.foto_perfil, e.avaliacao_media
        FROM educadores e
        JOIN utilizadores u ON e.utilizador_id = u.id
        WHERE e.aprovado = 1";

$params = [];
if ($distrito) {
    $sql .= " AND u.distrito = ?";
    $params[] = $distrito;
}
if ($preco_min) {
    $sql .= " AND e.preco_maximo >= ?";
    $params[] = $preco_min;
}
if ($preco_max) {
    $sql .= " AND e.preco_minimo <= ?";
    $params[] = $preco_max;
}
if ($especialidade) {
    $sql .= " AND EXISTS (SELECT 1 FROM educador_especialidades ee WHERE ee.educador_id = e.id AND ee.especialidade_id = ?)";
    $params[] = $especialidade;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
exit;
?>
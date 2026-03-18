<?php
require 'bd/bd.php';
$res = $conexao->query('SELECT id,palavra,categoria_id,status,usuario_id FROM termos ORDER BY id DESC LIMIT 10');
while ($r = $res->fetch_assoc()) {
    echo json_encode($r) . "\n";
}

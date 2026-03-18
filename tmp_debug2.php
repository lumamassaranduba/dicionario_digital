<?php
require 'bd/bd.php';
$res = $conexao->query('SELECT id,nome,email,tipo,categoria_id FROM usuarios');
while ($r = $res->fetch_assoc()) {
    echo json_encode($r) . "\n";
}

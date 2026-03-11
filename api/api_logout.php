<?php
session_start(); // Encontra a sessão atual

// Destrói todas as informações (nome, id, tipo)
session_unset();
session_destroy();

// Manda a pessoa de volta para a tela de login
header("Location: ../login.php");
exit;

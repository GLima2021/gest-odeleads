<?php
// Arquivo: conexao.php

$host = 'localhost';      // Endereço do servidor MySQL
$usuario = 'tayantad_leads';        // Nome de usuário do MySQL
$senha = 'Pinexpress123';              // Senha do MySQL
$banco = 'tayantad_leads'; // Nome do banco de dados

// Criar conexão
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conexao->connect_error) {
    die('Erro de conexão: ' . $conexao->connect_error);
}



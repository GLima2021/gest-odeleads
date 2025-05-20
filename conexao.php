<?php
// Arquivo: conexao.php

$host = 'HOST';      // Endereço do servidor MySQL
$usuario = 'USUARIO';        // Nome de usuário do MySQL
$senha = 'SENHA;              // Senha do MySQL
$banco = 'BANCO DE DADOS'; // Nome do banco de dados

// Criar conexão
$conexao = new mysqli($host, $usuario, $senha, $banco);

// Verificar conexão
if ($conexao->connect_error) {
    die('Erro de conexão: ' . $conexao->connect_error);
}



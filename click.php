<?php
include 'conexao.php';

// Verifica se já existe cookie do lead
if (isset($_COOKIE['vendedor_nome']) && isset($_COOKIE['vendedor_numero'])) {
    // Redireciona diretamente para o mesmo número
    $numero = $_COOKIE['vendedor_numero'];
    $mensagem = urlencode("Olá Tayan, Quero acesso ao Minicurso!");
    header("Location: https://wa.me/$numero?text=$mensagem");
    exit;
}

// Caso contrário, define os vendedores e escolhe um
$vendedores = [
    ["nome" => "Gabriela", "numero" => "5531971093699", "percentual" => 24],
    ["nome" => "Gabriel",  "numero" => "5541984975948", "percentual" => 19],
    ["nome" => "Cainã",    "numero" => "5531995094917", "percentual" => 13],
    ["nome" => "Hermes",   "numero" => "5544991181399", "percentual" => 15],
    ["nome" => "Tarles",   "numero" => "5531995452119", "percentual" => 9],
    ["nome" => "Anderson", "numero" => "5534984479415", "percentual" => 20]
];

function escolherVendedor($vendedores) {
    $listaDistribuida = [];
    foreach ($vendedores as $vendedor) {
        for ($i = 0; $i < $vendedor['percentual']; $i++) {
            $listaDistribuida[] = $vendedor;
        }
    }
    return $listaDistribuida[array_rand($listaDistribuida)];
}

$vendedorEscolhido = escolherVendedor($vendedores);
$nome = $vendedorEscolhido['nome'];
$numero = $vendedorEscolhido['numero'];
$data = date('Y-m-d H:i:s');


// Salva cookie por 30 dias
setcookie('vendedor_nome', $nome, time() + (30 * 24 * 60 * 60), "/");
setcookie('vendedor_numero', $numero, time() + (30 * 24 * 60 * 60), "/");

// Registra no banco
$stmt = $conexao->prepare("INSERT INTO click (nome, clique, numero, data) VALUES (?, 1, ?, ?)");
$stmt->bind_param("sss", $nome, $numero, $data);
$stmt->execute();

// Redireciona
$mensagem = urlencode("Olá Tayan, Quero acesso ao Minicurso!");
header("Location: https://wa.me/$numero?text=$mensagem");
exit;
?>

<?php

class Produto {
    private string $nome;
    public float $preco;
    public int $estoque;

    public function __construct(string $nome, float $preco, int $estoque) {
        $this->nome = $nome;
        $this->preco = $preco;
        $this->estoque = $estoque;
    }

    public function reduzirEstoque(int $quantidade): void {
        if ($this->estoque < $quantidade) {
            throw new Exception("Estoque insuficiente para {$this->nome}");
        }
        $this->estoque -= $quantidade;
    }
}

abstract class Cliente {
    public string $nome;
    public float $desconto;

    public function __construct(string $nome, float $desconto) {
        $this->nome = $nome;
        $this->desconto = $desconto;
    }
}

class ClientePremium extends Cliente {
    public function __construct(string $nome) {
        parent::__construct($nome, 0.10);
    }
}

class ClienteComum extends Cliente {
    public function __construct(string $nome) {
        parent::__construct($nome, 0.0);
    }
}

class ItemPedido {
    public Produto $produto;
    public int $quantidade;
    public float $subtotal;

    public function __construct(Produto $produto, int $quantidade) {
        $produto->reduzirEstoque($quantidade);
        $this->produto = $produto;
        $this->quantidade = $quantidade;
        $this->subtotal = $produto->preco * $quantidade;
    }
}

class Pedido {
    public Cliente $cliente;
    public array $itens = [];
    protected float $total = 0;
    public string $status = "Aberto";

    public function __construct(Cliente $cliente) {
        $this->cliente = $cliente;
    }

    public function adicionarProduto(Produto $produto, int $quantidade): void {
        $item = new ItemPedido($produto, $quantidade);
        $this->itens[] = $item;
        $this->total += $item->subtotal;
    }

    public function finalizar(string $status): void {
        $this->status = $status;
    }

    public function totalComDesconto(): float {
        return $this->total - ($this->total * $this->cliente->desconto);
    }

    public function mostrarResumo(): void {
        echo "<h3>Cliente: {$this->cliente->nome}</h3>";
        echo "<p>Total: R$ {$this->totalComDesconto()}</p>";
        echo "<p>Status: {$this->status}</p>";
    }
}

class NextOrder {
    public string $nome;
    public array $produtos = [];
    public array $clientes = [];
    public array $pedidos = [];

    public function __construct(string $nome) {
        $this->nome = $nome;
    }

    public function cadastrarProduto(Produto $produto): void {
        $this->produtos[] = $produto;
    }

    public function cadastrarCliente(Cliente $cliente): void {
        $this->clientes[] = $cliente;
    }

    public function criarPedido(Cliente $cliente): Pedido {
        $pedido = new Pedido($cliente);
        $this->pedidos[] = $pedido;
        return $pedido;
    }
}

$empresa = new NextOrder("NextOrder");

$caixa = new Produto("caixa de sapato", 11.99, 30);
$bonequinho = new Produto("bonequinho da friboi do luan santana", 9.99, 54);
$roda = new Produto("roda de carro", 10.50, 45);

$empresa->cadastrarProduto($caixa);
$empresa->cadastrarProduto($bonequinho);
$empresa->cadastrarProduto($roda);

$nicolas = new ClientePremium("nicolas");
$pedido1 = $empresa->criarPedido($nicolas);
$pedido1->adicionarProduto($caixa, 2);
$pedido1->finalizar("enviado");
$pedido1->mostrarResumo();

$fernado = new ClienteComum("fernado");
$pedido2 = $empresa->criarPedido($fernado);
$pedido2->adicionarProduto($bonequinho, 3);
$pedido2->finalizar("pago");
$pedido2->mostrarResumo();

$alexandre = new ClienteComum("alexandre");
$pedido2 = $empresa->criarPedido($alexandre);
$pedido2->adicionarProduto($roda, 3);
$pedido2->finalizar("aberto");
$pedido2->mostrarResumo();

echo "<br>Estoque caixa: {$caixa->estoque}";
echo "<br>Estoque bonequinho: {$bonequinho->estoque}";
echo "<br>Estoque roda: {$roda->estoque}";

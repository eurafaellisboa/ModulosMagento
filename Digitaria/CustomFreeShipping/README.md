# Módulo de Frete Grátis por Faixa de CEP e valor mínimo por faixa de CEP | Magento 2

O módulo permite que seja definido Frete Grátis ou com um valor fixo a partir de uma determinada faixa de CEP, além de possibiliar que seja escolhido quais grupos de clientes poderão ver o método de entrega.

Para instalar o módulo é muito fácil.

### Passo 1: Faça o Download do módulo

### Passo 2: Faça o upload dos arquivos no seu servidor

Utilizando seu cliente de FTP preferido

### Passo 3: Ative o módulo

No terminal, execute o comando 
```
php bin/magento module:enable Digitaria_CustomFreeShipping
```

### Passo 4: Atualize o banco de dados

No terminal, execute o comando 
```
php bin/magento setup:upgrade
```

### Passo 5: Configuração do módulo

Dentro do ADMIN da sua loja vá até

Loja - Configuração - Vendas - Entrega ou Método de Entrega

Além das configurações básicas do módulo existem dois campos

#### Faixas de CEP e valor mínimo

Aqui devem ser inseridas a faixa de CEP inicial, final e o valor mínimo para essa faixa de CEP, por exemplo:

80000000,89999999,149.99

Onde 80000000 (faixa inicial), 89999999 (faixa final) e 149.99 (valor mínimo para ser exibido).

#### Grupos de Clientes

Depois disso, no campo Grupos de clientes permitidos, basta selecionar quais grupos de clientes poderão usar o método de entrega


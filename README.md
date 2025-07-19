# 🧩 Instalação do Projeto Laravel

## ⚙️ Pré-requisitos

- PHP >= 8.x
- Composer
- MySQL
- Laravel instalado globalmente (opcional)

---

## 🚀 Passos para rodar o projeto localmente

### 1. Instalar dependências do Laravel

No terminal, dentro da pasta do projeto:

```bash
composer install


---

### 2. Criar e configurar o banco de dados

- Na linha de comando do mysql : create database montink

Edite o arquivo `.env` na raiz do projeto e ajuste as variáveis de conexão com o MySQL:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario

### 3. Gerar a chave da aplicação

php artisan key:generate

---

### 4. Executar as migrations

php artisan migrate

---

### 5. Popular o banco com dados iniciais (seeds)

php artisan db:seed

---

### 6. Iniciar o servidor de desenvolvimento
php artisan serve


---

### 7. Acessar a aplicação no navegador

Abra [http://localhost:8000](http://localhost:8000)

---

## ✅ Pronto!

Seu ambiente Laravel está configurado e em execução localmente.

### Para executar testes.

- Acesso Menu Produtos e cadastre um novo (incluindo variações ou não).
- Clique comprar.
- Selecione a varição (se houver) e quantidade para adicionar ao carrinho.
- Acesso Menu Cupons, selecione um cupom válido para aplicar ao carrinho.
- Acesso novamente o carrinho, verifique o desconto e finalize informando cep e email .

### Atualizando status do pedido.

curl --request POST \
  --url http://localhost:8000/webhook/pedido-status \
  --header 'Content-Type: application/x-www-form-urlencoded' \
  --data id=1 \
  --data status=cancelado



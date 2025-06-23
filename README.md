# Lumen PHP Framework - Clickbeard

# Dependencias usdadas no projeto
```bash
darkaonline/l5-swagger  2.1.3   
fakerphp/faker          1.24.1  
laravel/lumen-framework 10.0.4  
mockery/mockery         1.6.12  
phpunit/phpunit         10.5.46 
tymon/jwt-auth          2.2.1   
```

# API  Clickbeard - Como Executar

- Baixar as dependencias.
```bash
    composer install
```
- Configura seu banco de dados e rode o create database. 
- Crie o arquivo .env basedo no seu exemplo
``` .env
    #configure aqui    
    DB_CONNECTION=mysql
    DB_HOST=192.168.56.57   
    DB_PORT=3306
    DB_DATABASE=clickbeard_db
    DB_USERNAME=homestead
    DB_PASSWORD=secret

    JWT_SECRET=
```
- Execute as migrations
```bash
    php artisan migrate
```

- Caso queira pode rodar as seeder que vão popular o banco de dados.
```bash
    php artisan db:seed
```

- Ou rodar os dois de uma vez
```bash
    php artisan migrate --seed
```
- Caso ja tenha usado o comando de migrate, pode recriar tudo e exeutar tudo denovo
```bash
    php artisan migrate:fresh --seed
```
- Cria o JWT secret, comando para criar o hash
- pode ser criado de duas formas usando openssl ou php
- OpenSSL, presente no terminal do Git bash
```bash
    openssl rand -hex 32
```
- Com php dessa forma 
```bash
    php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```
- Ambos os comandos geran um hash como esse: 
```bash
ec6f775c0e1270947013c5d2a912b5df062aa35d4d3e681b176fb81976579750
```
- Basta pegar esse hash e colocar no JWT_SECRET dentro do .env

- Use o servidor embutido do php para executar a aplicação
```bash
    php -S localhost:8001 -t ./public
```
- Lembre-se de configurar o timezone da aplicação no .env, coloque ```America/Sao_Paulo```. 




# Lumen PHP Framework - Clickbeard

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

-Caso queira pode rodar as seeder que populan o banco
```bash
    php artisan db:seed
```

- Ou rodar os dois de uma vez
```bash
    php artisan migrate --seed
```
- Caso ja tenha usado pode recriar tudo e eceutar tudo denovo
```bash
    php artisan migrate:fresh --seed
```
- Cria o JWT secret
##comando para crair o hash
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
    php -S localhost:8000 -t ./public
```





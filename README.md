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

- Cria o JWT secret
##comando para crair o hash

- Use o servidor embutido do php para executar a aplicação
```bash
    php -S localhost:8000 -t /public
```





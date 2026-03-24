# portfolio-backend-api

[![SymfonyInsight](https://insight.symfony.com/projects/709f18f2-fca7-4820-a5d7-786849b28e55/big.svg)](https://insight.symfony.com/projects/709f18f2-fca7-4820-a5d7-786849b28e55)

### Usage / Access
  
| Service               | URL                       |
| --------------------- |:-------------------------:|
| API                   | http://localhost:8080     |
| LiteSpeed Admin       | http://localhost:7080     |
| RabbitMQ UI           | http://localhost:15672    |
| Mailhog UI            | http://localhost:8025     |
| phpMyAdmin            | http://localhost:8081     |


###### Always run these 3 before pushing anything:
- `docker compose exec php vendor/bin/phpunit --testdox`
- `docker compose exec php php -d memory_limit=512M vendor/bin/phpstan analyse`
- `docker compose exec php vendor/bin/php-cs-fixer fix`

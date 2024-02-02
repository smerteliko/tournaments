Развернуть проект можно двумя способами - докер и докер через make

Docker (в скобках будет make) -
1) `docker compose -f ./docker/docker-compose.yml build` (`make build`)
2) `docker compose -f ./docker/docker-compose.yml up` (`make up`)
3) `docker inspect *tournaments_default* `(или другую сеть которую создал докер)
4) Взять ip *nginx* это основной ip на котором будет висеть приложение
5) Взять ip *databases* и прописать в *.env* в поле  CHANGE_ME `mysql://root:root@CHANGE_ME:3306/tournaments?allowPublicKeyRetrieval=true&serverVersion=8.3.0&charset=utf8mb4`

Оставшаяся настройка - миграции:
1) `docker compose -f ./docker/docker-compose.yml  exec -u www-data php-fpm bin/console doctrine:migrations:diff --no-interaction`
   (`make db_diff`) Для создание таблицы миграциии
2) `docker compose -f ./docker/docker-compose.yml  exec -u www-data php-fpm bin/console doctrine:migrations:migrate --no-interaction` (`make db_migrate`) Для миграции

Далее заходим на хост NGINX и используем приложение

Руты 

* / - список турниров
* /teams/ - список команд
* /tournaments/ - список турниров
* /tournaments/{slug} поиск по названию турнира
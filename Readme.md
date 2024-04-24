# **Легкий генератор встреч команд для разных турниров которые мы добавляем.**
Пример для 6 команды:

| 1й день  | 2й день | 3й день | 4й день | 5й день |
|----------| ------- |---------|---------|---------|
| k1 : k2  | k1 : k3 | k1 : k4 | k1 : k5 | k1 : k6 |
| k5 : k6  | k2 : k6 | k2 : k5 | k4 : k6 | k3 : k5 |
| k3 : k4  | k5 : k4 | k6 : k3 | k3 : k2 | k4 : k2 |


### Развернуть проект можно двумя способами - докер и докер через make

#### Сборка вручную: 

Docker (в скобках будет make) -
1) `docker compose -f ./docker/docker-compose.yml build` (`make build`)
2) `docker compose -f ./docker/docker-compose.yml up` (`make up`)

Оставшаяся настройка - миграции:
1) `docker compose -f ./docker/docker-compose.yml  exec -u www-data php-fpm bin/console doctrine:migrations:diff --no-interaction`
   (`make db_diff`) Для создание таблицы миграциии
2) `docker compose -f ./docker/docker-compose.yml  exec -u www-data php-fpm bin/console doctrine:migrations:migrate --no-interaction` (`make db_migrate`) Для миграции


#### Сборка автоматически:

сборка `make app_build`

удаление  `make app_down`

### Руты 

* / - список турниров
* /teams/ - список команд
* /tournaments/ - список турниров
* /tournaments/{slug} поиск по названию турнира
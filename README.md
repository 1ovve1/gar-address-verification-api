# Сервис верификации адресной информации на основе ГАР БД ФИАС
### Описание
Данный сервис включает в себя: 
- Средства для создания и организации локальной БД, используя исходники ГАР БД ФИАС;
- Реализацию простого RESTful-сервиса, осуществляющего верификацию пользовательских адресных данных или получения специальных кодов.
### Конфигурирование проекта
Проект использует файл ".env" для получения глобальных конфигурационных переменных. 
Пример файла конфигурации представлен в файле "./env.example".

Используйте следующую команду перед настройкой проекта: 
```bash 
cp ./.env.example ./.env
```

Более "комплесные" настройки представленны в директории "./config/", в числе которых:
+ ***drivers/**** - основные шаблоны, согласно которым функционирует конструктор SQL-запросов;
+ ***migration.php*** - списки классов-моделей, которые подлежат миграции;
+ ***regions.php*** - список регионов, которые необходимо загрузить из исходников ГАР БД ФИАС;
+ ***xml_handlers_config.php*** - список обработчиков, осуществляющих парсинг xml-документов архива ГАР БД ФИАС;

Если вы используете сервис для загрузки данных из ГАР БД ФИАС в локальную БД, необходимо **разместить архив по пути ./resources/archive/gar_example.zip** или указать собственные пути в файле конфигурации .env!

### Взаимодействие с помощью командной строки
Для взаимодействия с сервисом используется следующий интерфейс:
```bash
./gar <команда> [параметры] 
```
Основные наборы команд:
+ Получение справки по существующему функционалу:
```bash
./gar help 
```
+ Миграция и загрузка данных в локальную БД:
```bash
./gar upload -m 
```
+ Пересоздать текущую структуру базы данных и начать загрузку 8-го и 17-го региона, используя два потока:
```bash
./gar upload --migrate-recreate -r 8,17 -t 2 
```
+ Запустить сервер в (зависимости от конфигурационного файла будет запущен встроенный тестовый сервер php или SWOOLE):
```bash
./gar serve 
```

### Структура базы данных по умолчанию
![Структура базы данных](docs/img/scheme_08_10_22.png)
На 08.10.22 производиться формаирование следующих таблиц:
+ Адресные объекты:
  + ***addr_obj*** - адресные объекты;
  + ***addr_obj_levels*** - категории адресных объектов по их уровням;
  + ***addr_obj_typenames*** - категории адресных объектов по типу (город, район, улица и т.п.);
  + ***addr_obj_params*** - таблица с описанием параметров для конкретного адресного объекта;
  + ***addr_obj_params_types*** - категории параметров адресных объектов;
  + ***addr_obj_by_addr_obj_hierarchy*** - иерархия адресных объектов в формате ~~master~~ "родитель" => "ребёнок"
+ Дома:
  + ***houses*** - дома;
  + ***houses_type*** - категории домов по их типу;
  + ***houses_addtype*** - дополнительные категории домов по их типу;
  + ***houses_by_addr_obj_hierarchy*** - карта домов, относящихся к конкретных адресных объектам;

### Демонстрационный REST API сервис
Демонстрационный сервис в проекте расчитан на работу с использованием фреймворка SWOOLE, но ничего не мешает Вам отключить эту опцию SWOOLE_SERVER_ENABLE в .env файле и использовать NGINX или APACHE через точку входа **./public/index.php**.

Ожидаемые запросы:
+ Разбор пользовательского адреса:
```
/<номер_региона>/address?address="..."
```
+ Получение кода (или всех кодов, используя тип "all") по конкретному OBJECTID или АДРЕСУ:
```
/<номер_региона>/code/<тип_кода>?address="..."  (по адресу)
/<номер_региона>/code/<тип_кода>?&objectid="..." (по objectid)
```

Пример запроса для получения верифицированного адреса из "калм,лаганс,кра,кра" в 8 регионе:
```
http://0.0.0.0:9501/08/address?address=калм,лаганс,кра,кра
```

Ответ:

![Ответ на http://0.0.0.0:9501/08/address?address=калм,лаганс,кра,кра](docs/img/json_response_1.png)

Пример запроса для получения всех доступных кодов по аналогичному адресу:
```
http://0.0.0.0:9501/08/code/all?address=калм,лаганс,кра,кра
```

Формат JSON-ответа:

![Ответ на http://0.0.0.0:9501/08/code/all?address=калм,лаганс,кра,кра](docs/img/json_response_2.png)

Аналогичный ответ можно получить, используя objectid последнего объекта:
```
http://0.0.0.0:9501/08/code/all?objectid=114436
```
### Конфигурирование
Перменные самой среды должны быть объявлены в .env файле согласно .env.example.

Конфигурация загрузчика ГАР и миграций определены в директории ./config:

<pre><font color="#12488B"><b>./config</b></font>
├── migration.php [описание миграций]
├── regions.php   [список регионов]
└── xml_handlers_config.php [список обработчиков]
</pre>

Обработчики представлены по пути ./cli/XMLParser/Files в двух директориях:
- ByRegions - те файлы, которые требуется обработать для каждого региона;
- ByRoot - те файлы, которые требуется обработать единожды (корневые файлы описаний).

Имена обработчиков соответствуют префиксу имен файлов в самом архиве. На данный момент реализованны следующие обработчики:
<pre><font color="#12488B"><b>cli/XMLParser/Files/</b></font>
├── <font color="#12488B"><b>ByRegions</b></font>
│   ├── AS_ADDR_OBJ_PARAMS.php
│   ├── AS_ADDR_OBJ.php
│   ├── AS_ADM_HIERARCHY.php
│   ├── AS_HOUSES.php
│   └── AS_MUN_HIERARCHY.php
├── <font color="#12488B"><b>ByRoot</b></font>
    ├── AS_ADDHOUSE_TYPES.php
    ├── AS_ADDR_OBJ_TYPES.php
    ├── AS_HOUSE_TYPES.php
    ├── AS_OBJECT_LEVELS.php
    └── AS_PARAM_TYPES.php
</pre>

### Тесты

Интеграционные тесты находятся по пути *./tests*. Файл конфигурации phpunit заранее определён, так что запуск тестов можно осуществлять вызовом *./vendor/bin/phpunit*. Для проверки необходимо загрузить базу через *./cli upload* и иметь подключение к бд.

*!!ВНИМАНИЕ!!*: тесты привязаны к контексту и проверяют функционал приложения на основе действующих данных из 8-го региона (Калмыкия). Тестовый набор данных можно загрузить из [следующего репозитория](https://github.com/1ove1/gar-example) (если нет желания скачивать полный архив на 30гб+).


### Заключение
Сервис всё ещё дорабатывается, данный readme скорее заглушка, но всё же это лучше чем голый репозиторий)
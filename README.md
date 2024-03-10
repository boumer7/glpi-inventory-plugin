## Модификация плагина отчётов для GLPI (менеджера IT-инфраструктуры)

![logo-glpi-bleu-1](https://github.com/boumer7/glpi-inventory-plugin/assets/33152397/4b97ef85-ce5f-4e26-8a5d-f13f8165d499)

**Стек: PHP, SQL-запросы, HTML, CSS.**

[Перейти сразу к файлам](#примеры-моих-отчётов)

Для более подробного ознакомления с GLPI рекомендую посмотреть следующие материалы:
* [Официальный сайт GLPI](https://glpi-project.org/)
* [GLPI простыми словами](https://www.dmosk.ru/terminus.php?object=glpi)
* [GLPI в организации. Статья на Habr.](https://habr.com/ru/articles/312522/)

[Официальный портал плагинов GLPI](https://plugins.glpi-project.org/#/)

GLPI зарекомендовал себя во многих компаниях как отличное решение для менеджмента, мониторинга и инвентаризации всего парка компьютерного оборудования.

## Проблема
В некоторых случаях желаемый формат вывода каких-либо данных не предполагается системными настройками GLPI, поэтому возникает необходимость разработки собственных надостроек для решения этой проблемы.

## Практическая значимость проекта
Этот проект представляет из себя модификацию уже существующего плагина для отчётов GLPI — [**reports**](https://github.com/yllen/reports)

Посредством моего проекта можно сформировать представление о том, как модифицировать этот плагин под рабочие задачи организации, бизнес-требования для того, чтобы данные выводились в необходимом формате.

Директория плагина выглядит следующим образом (может отличаться в зависимости от версии):

![image](https://github.com/boumer7/glpi-inventory-plugin/assets/33152397/eff6e911-edbb-48a2-aa63-54e255a65df4)


Основная суть модификации плагина под свои нужды заключается в написании кода отчёта на основе шаблона на PHP, SQL и местами HTML+CSS.
Код отчёта создаётся в отдельной папке с соответствующим желаемым названием в директории **report**.

Примеры уже готовых отчётов внутри плагина можно посмотреть в этой же папке, например, код для отчёта принтеров в printers/printers.php

Самой важной, пожалуй, вещью является понимание структуры таблиц в БД MySQL GLPI и написание соответствующих запросов к БД на SQL.
Более подробно можно с ней ознакомиться в [документации GLPI для разработчиков](https://glpi-developer-documentation.readthedocs.io/en/master/devapi/database/dbmodel.html), однако там описаны не все связи и таблицы, поэтому для решения узкоспециализированнных задач придётся проанализировать архитектуру самостоятельно или использовать написанные мною отчёты для лучшего представления.

![image](https://glpi-developer-documentation.readthedocs.io/en/master/_images/db_model_computer.png)

<a id="examples"></a>
## Примеры моих отчётов

* [vtrm_combined.php](https://github.com/boumer7/glpi-inventory-plugin/blob/main/report/vtrm_combined/vtrm_combined.php) — отчёт для вывода связанных между собой бюджетов, комплексов и компьютеров, которые в них входят. Задействование таблиц glpi_infocoms, glpi_budgets, glpi_appliances, glpi_appliances_items, glpi_peripherals, glpi_printers, glpi_racks, glpi_networkequipments, glpi_passivedcequipments;
* [vtrm_report.php](https://github.com/boumer7/glpi-inventory-plugin/blob/main/report/vtrm_report/vtrm_report.php) — отчёт для вывода объектов из отчёта выше, иная модификация. Задействование таблиц glpi_infocoms, glpi_budgets, glpi_appliances, glpi_peripherals, glpi_printers, glpi_racks;
* [vtrm_report_appliance.php](https://github.com/boumer7/glpi-inventory-plugin/blob/main/report/vtrm_report_appliance/vtrm_report_appliance.php)  — отчёт для вывода комплексов и связанных с ним оборудования. Задействование таблиц glpi_locations, glpi_budgets, glpi_users, glpi_budgettypes;
* [report_moves.php](https://github.com/boumer7/glpi-inventory-plugin/blob/main/report/report_moves/report_moves.php) — отчёт для вывода историй перемещений оборудования (только отсоединения, например, монитор был отключён от ПК). Задействование таблиц glpi_logs, glpi_monitors, glpi_printers, glpi_peripherals, glpi_racks, glpi_operatingsystems, glpi_networkequipments, glpi_computers_items.

Расшифровка предназначений вышеупомянутых таблиц:
* **glpi_logs** — массивная по объёму таблица, содержащая все логи системы. Начиная от системных изменений, заканчивая отсоединением одного оборудования от другого. Для извлечения из неё данных необходимо использовать определённые условия;
* glpi_budgets — таблица, служащая для хранения данных о бюджетах объектов;
* glpi_budgettypes — таблица, напрямую связанная с glpi_budgets, служит для хранения типов бюджетов;
* glpi_appliances — таблица, служащая для хранения данных о комплексах оборудования;
* glpi_appliances_items — таблица, служащая для хранения данных о составах комплексов;
* glpi_computers — таблица, служащая для хранения данных о компьютерах;
* glpi_computers_items — таблица, служащая для хранения данных о сборках компьютеров, подключенных устройств;
* glpi_monitors — таблица, служащая для хранения данных о мониторах;
* glpi_peripherals — таблица, служащая для хранения данных о периферии;
* glpi_printers — таблица, служащая для хранения данных о принтерах;
* glpi_racks — таблица, служащая для хранения данных о стойках;
* glpi_networkequipments — таблица, служащая для хранения данных о сетевом оборудовании;
* glpi_users — таблица, служащая для хранения данных о пользователях оборудования;
* glpi_locations — таблица, служащая для хранения данных о местоположении оборудования;

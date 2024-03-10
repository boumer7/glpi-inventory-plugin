<?php
$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

$report = new PluginReportsAutoReport('Отчёт по ВТРМ Комплексам');

# $grpcrit = new PluginReportsGroupCriteria($report, 'budgettype.name', '', 'is_value');
$loccrit = new PluginReportsLocationCriteria($report, 'glpi_locations.completename');

$report->displayCriteriasForm();

if ($report->criteriasValidated()) {
   $report->setSubNameAuto();

   $cols = [
    new PluginReportsColumn('budgettype', 'Бюджет'),
    new PluginReportsColumn('budgetname', 'Номер ВТРМ'),
    new PluginReportsColumn('appliance_name', 'Наименование комплекса'),
    new PluginReportsColumn('tech_user', 'Ответственный специалист'),
    new PluginReportsColumn('itemtype_rus', 'Тип'),
    new PluginReportsColumn('object_name', 'Наименование'),
    new PluginReportsColumn('serial_number', 'Серийный номер'),
    new PluginReportsColumn('inventory_number', 'Инвентарный номер'),
    new PluginReportsColumn('contact', 'Контакт'),
    new PluginReportsColumn('object_user', 'Пользователь'),
    new PluginReportsColumn('object_location', 'Местоположение'),
    new PluginReportsColumn('datemod', 'Последнее изменение')
   ];

$sql = "
        SELECT 
        bdgt.name AS budgettype,
        bdg.name AS budgetname,

        CASE appl_item.itemtype
        WHEN 'Computer' THEN 'Компьютер'
        WHEN 'Monitor' THEN 'Монитор'
        WHEN 'Peripheral' THEN 'Периферия'
        WHEN 'Printer' THEN 'Принтер'
        ELSE '' END AS itemtype_rus,

        CASE
          WHEN appl_item.itemtype = 'Computer' THEN comp.name
          WHEN appl_item.itemtype = 'Monitor' THEN mon.name
          WHEN appl_item.itemtype = 'Peripheral' THEN per.name
        END AS object_name,

        CASE
          WHEN appl_item.itemtype = 'Computer' THEN comp.serial
          WHEN appl_item.itemtype = 'Monitor' THEN mon.serial
          WHEN appl_item.itemtype = 'Peripheral' THEN per.serial
        END AS serial_number,

        CASE
          WHEN appl_item.itemtype = 'Computer' THEN comp.otherserial
          WHEN appl_item.itemtype = 'Monitor' THEN mon.otherserial
          WHEN appl_item.itemtype = 'Peripheral' THEN per.otherserial
        END AS inventory_number,

        CASE
          WHEN appl_item.itemtype = 'Computer' THEN comp.contact
          WHEN appl_item.itemtype = 'Monitor' THEN mon.contact
          WHEN appl_item.itemtype = 'Peripheral' THEN per.contact
        END AS contact,

        CASE
          WHEN appl_item.itemtype = 'Computer' THEN comp.date_mod
          WHEN appl_item.itemtype = 'Monitor' THEN mon.date_mod
          WHEN appl_item.itemtype = 'Peripheral' THEN per.date_mod
        END AS datemod,

        glpi_locations.completename AS object_location,

        appl.name AS appliance_name,

        CONCAT(glpi_users.firstname, ' ', glpi_users.realname) AS object_user,
        CONCAT(tech_user.firstname, ' ', tech_user.realname) AS tech_user

        
        FROM glpi_budgets bdg

        LEFT JOIN glpi_infocoms info ON bdg.id = info.budgets_id
        LEFT JOIN glpi_appliances appl ON info.items_id = appl.id
        LEFT JOIN glpi_appliances_items appl_item ON appl.id = appl_item.appliances_id

        LEFT JOIN glpi_computers comp ON appl_item.itemtype = 'Computer' AND appl_item.items_id = comp.id
        LEFT JOIN glpi_monitors mon ON appl_item.itemtype = 'Monitor' AND appl_item.items_id = mon.id
        LEFT JOIN glpi_peripherals per ON appl_item.itemtype = 'Peripheral' AND appl_item.items_id = per.id

        LEFT JOIN glpi_locations 
        ON (appl_item.itemtype = 'Computer' AND comp.locations_id = glpi_locations.id)
        OR (appl_item.itemtype = 'Monitor' AND mon.locations_id = glpi_locations.id)
        OR (appl_item.itemtype = 'Peripheral' AND per.locations_id = glpi_locations.id)

        LEFT JOIN glpi_users ON 
        CASE
            WHEN appl_item.itemtype = 'Computer' THEN comp.users_id
            WHEN appl_item.itemtype = 'Monitor' THEN mon.users_id
            WHEN appl_item.itemtype = 'Peripheral' THEN per.users_id
            ELSE NULL
        END = glpi_users.id

        LEFT JOIN glpi_users as tech_user ON 
        CASE
            WHEN appl_item.itemtype = 'Computer' THEN comp.users_id_tech
            WHEN appl_item.itemtype = 'Monitor' THEN mon.users_id_tech
            WHEN appl_item.itemtype = 'Peripheral' THEN per.users_id_tech
            ELSE NULL
        END = tech_user.id

        LEFT JOIN glpi_budgettypes bdgt ON bdgt.id = bdg.budgettypes_id

        WHERE info.itemtype = 'Appliance'
        ORDER BY budgetname;
";

$report->setColumns($cols);
$report->setSqlRequest($sql);

$report->execute();

} else {
   Html::footer();
}

?>
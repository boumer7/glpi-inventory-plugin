<?php
$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

$report = new PluginReportsAutoReport(__('vtrm_report_title', 'reports'));

# $grpcrit = new PluginReportsGroupCriteria($report, 'budgettype.name', '', 'is_value');
$loccrit = new PluginReportsLocationCriteria($report, 'glpi_locations.completename');


$report->displayCriteriasForm();

if ($report->criteriasValidated()) {
   $report->setSubNameAuto();

   $cols = [
    new PluginReportsColumn('budgettype', 'Бюджет'),
    new PluginReportsColumn('budgetname', 'Номер ВТРМ'),
    new PluginReportsColumn('tech_user', 'Ответственный специалист'),
    new PluginReportsColumn('itemtype_rus', 'Тип'),
    new PluginReportsColumn('object_name', 'Наименование'),
    new PluginReportsColumn('serial_number', 'Серийный номер'),
    new PluginReportsColumn('inventory_number', 'Инвентарный номер'),
    new PluginReportsColumn('contact', 'Контакт'),
    new PluginReportsColumn('object_user', 'Пользователь'),
    new PluginReportsColumn('object_location', 'Местоположение'),
    new PluginReportsColumn('date_mod', 'Последнее изменение')
   ];

$sql = "
SELECT
    budgets.name AS budgetname,
    budgettypes.name AS budgettype,
    CASE info.itemtype
        WHEN 'Computer' THEN 'Компьютер'
        WHEN 'Monitor' THEN 'Монитор'
        WHEN 'Peripheral' THEN 'Периферия'
        WHEN 'Appliance' THEN 'Комплекс'
        WHEN 'Printer' THEN 'Принтер'
        WHEN 'Rack' THEN 'Каркас'
    END AS itemtype_rus,
    CASE
        WHEN info.itemtype = 'Computer' THEN comp.name
        WHEN info.itemtype = 'Monitor' THEN mon.name
        WHEN info.itemtype = 'Peripheral' THEN per.name
        WHEN info.itemtype = 'Appliance' THEN appl.name
        WHEN info.itemtype = 'Printer' THEN printer.name
        WHEN info.itemtype = 'Rack' THEN rack.name
    END AS object_name,
    CASE
        WHEN info.itemtype = 'Computer' THEN comp.serial
        WHEN info.itemtype = 'Monitor' THEN mon.serial
        WHEN info.itemtype = 'Peripheral' THEN per.serial
        WHEN info.itemtype = 'Appliance' THEN appl.serial
        WHEN info.itemtype = 'Printer' THEN printer.serial
        WHEN info.itemtype = 'Rack' THEN rack.serial
    END AS serial_number,
    CASE
        WHEN info.itemtype = 'Computer' THEN comp.date_mod
        WHEN info.itemtype = 'Monitor' THEN mon.date_mod
        WHEN info.itemtype = 'Peripheral' THEN per.date_mod
        WHEN info.itemtype = 'Appliance' THEN appl.date_mod
        WHEN info.itemtype = 'Printer' THEN printer.date_mod
        WHEN info.itemtype = 'Rack' THEN rack.date_mod
    END AS date_mod,
    CASE
        WHEN info.itemtype = 'Computer' THEN comp.otherserial
        WHEN info.itemtype = 'Monitor' THEN mon.otherserial
        WHEN info.itemtype = 'Peripheral' THEN per.otherserial
        WHEN info.itemtype = 'Appliance' THEN appl.otherserial
        WHEN info.itemtype = 'Printer' THEN printer.otherserial
        WHEN info.itemtype = 'Rack' THEN rack.otherserial
    END AS inventory_number,
    CONCAT(glpi_users.firstname, ' ', glpi_users.realname) AS object_user,
    CASE
        WHEN info.itemtype = 'Computer' THEN comp.contact
        WHEN info.itemtype = 'Monitor' THEN mon.contact
        WHEN info.itemtype = 'Peripheral' THEN per.contact
        WHEN info.itemtype = 'Appliance' THEN appl.contact
        WHEN info.itemtype = 'Printer' THEN printer.contact
    END AS contact,
    glpi_locations.completename AS object_location,
    CONCAT(tech_user.firstname, ' ', tech_user.realname) AS tech_user
FROM
    glpi_budgets budgets
LEFT JOIN
    glpi_infocoms info ON budgets.id = info.budgets_id
LEFT JOIN
    glpi_appliances appl ON info.items_id = appl.id
LEFT JOIN
    glpi_computers comp ON info.itemtype = 'Computer' AND info.items_id = comp.id
LEFT JOIN
    glpi_monitors mon ON info.itemtype = 'Monitor' AND info.items_id = mon.id
LEFT JOIN
    glpi_peripherals per ON info.itemtype = 'Peripheral' AND info.items_id = per.id
LEFT JOIN
    glpi_printers printer ON info.itemtype = 'Printer' AND info.items_id = printer.id
LEFT JOIN
    glpi_racks rack ON info.itemtype = 'Rack' AND info.items_id = rack.id
LEFT JOIN
    glpi_locations ON (info.itemtype = 'Computer' AND comp.locations_id = glpi_locations.id)
                OR (info.itemtype = 'Monitor' AND mon.locations_id = glpi_locations.id)
                OR (info.itemtype = 'Peripheral' AND per.locations_id = glpi_locations.id)
                OR (info.itemtype = 'Appliance' AND appl.locations_id = glpi_locations.id)
                OR (info.itemtype = 'Printer' AND printer.locations_id = glpi_locations.id)
LEFT JOIN
    glpi_users ON CASE
        WHEN info.itemtype = 'Computer' THEN comp.users_id
        WHEN info.itemtype = 'Monitor' THEN mon.users_id
        WHEN info.itemtype = 'Peripheral' THEN per.users_id
        WHEN info.itemtype = 'Appliance' THEN appl.users_id
        WHEN info.itemtype = 'Printer' THEN printer.users_id
    END = glpi_users.id
LEFT JOIN
    glpi_users AS tech_user ON CASE
        WHEN info.itemtype = 'Computer' THEN comp.users_id_tech
        WHEN info.itemtype = 'Monitor' THEN mon.users_id_tech
        WHEN info.itemtype = 'Peripheral' THEN per.users_id_tech
        WHEN info.itemtype = 'Appliance' THEN appl.users_id_tech
        WHEN info.itemtype = 'Printer' THEN printer.users_id_tech
        WHEN info.itemtype = 'Rack' THEN rack.users_id_tech
    END = tech_user.id
LEFT JOIN
    glpi_budgettypes budgettypes ON budgets.budgettypes_id = budgettypes.id

WHERE info.itemtype is not null
ORDER BY date_mod DESC;

";

$report->getOrderBy('budgettype');

$report->setColumns($cols);
$report->setSqlRequest($sql);

$report->execute();

} else {
   Html::footer();
}

?>
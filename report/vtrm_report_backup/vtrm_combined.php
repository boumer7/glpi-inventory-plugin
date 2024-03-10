<?php
$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

$report = new PluginReportsAutoReport('ВТРМ Совмещённый отчёт');

$budgettype_crit = new PluginReportsBudgetTypeCriteria($report, 'glpi_infocoms.budgets_id');

$report->displayCriteriasForm();

if ($report->criteriasValidated()) {
   $report->setSubNameAuto();

   $cols = [
    new PluginReportsColumn('budget_type', 'Бюджет', [
                                         'sorton' => 'glpi_budgettypes.id']), 
    new PluginReportsColumn('budget_name', 'ВТРМ', [
                                         'sorton' => 'glpi_budgets.id']),
    new PluginReportsColumn('appliance_name', 'Имя комплекса'),
    new PluginReportsColumn('itemtype_rus', 'Тип'),
    new PluginReportsColumn('object_name', 'Имя объекта'),
    new PluginReportsColumn('otherserial', 'Инвентарный номер'),
    new PluginReportsColumn('serial_num', 'Серийный номер'),
    new PluginReportsColumn('contact', 'Контакт'),
    new PluginReportsColumn('object_user', 'Пользователь'),
    new PluginReportsColumn('tech_user', 'Ответственный специалист'),
    new PluginReportsColumn('object_location', 'Местоположение'),
    new PluginReportsColumn('date_mod', 'Последнее изменение')
   ];

$sql = "
SELECT 
gbt.name as budget_type,
gb.name as budget_name, 
ga.name as appliance_name, 

CASE
WHEN gai.itemtype = 'Computer' THEN 'Компьютер'
WHEN gai.itemtype = 'Monitor' THEN 'Монитор'
WHEN gai.itemtype = 'Peripheral' THEN 'Периферия'
 
WHEN gi.itemtype = 'Computer' THEN 'Компьютер'
WHEN gi.itemtype = 'Monitor' THEN 'Монитор'
WHEN gi.itemtype = 'Peripheral' THEN 'Периферия'
WHEN gi.itemtype = 'Printer' THEN 'Принтер'
WHEN gi.itemtype = 'Rack' THEN 'Каркас'
END AS itemtype_rus,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.name
WHEN gai.itemtype = 'Monitor' THEN gm.name
WHEN gai.itemtype = 'Peripheral' THEN gp.name

WHEN gi.itemtype = 'Computer' THEN info_gc.name
WHEN gi.itemtype = 'Monitor' THEN info_gm.name
WHEN gi.itemtype = 'Peripheral' THEN info_gp.name
WHEN gi.itemtype = 'Printer' THEN info_gpr.name
WHEN gi.itemtype = 'Rack' THEN info_r.name
END AS object_name,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.date_mod
WHEN gai.itemtype = 'Monitor' THEN gm.date_mod
WHEN gai.itemtype = 'Peripheral' THEN gp.date_mod

WHEN gi.itemtype = 'Computer' THEN info_gc.date_mod
WHEN gi.itemtype = 'Monitor' THEN info_gm.date_mod
WHEN gi.itemtype = 'Peripheral' THEN info_gp.date_mod
WHEN gi.itemtype = 'Printer' THEN info_gpr.date_mod
WHEN gi.itemtype = 'Rack' THEN info_r.date_mod
END AS date_mod,

-- gai - Объекты комплекса; gi - Объекты ВТРМ
CASE
WHEN gai.itemtype = 'Computer' THEN gc.otherserial
WHEN gai.itemtype = 'Monitor' THEN gm.otherserial
WHEN gai.itemtype = 'Peripheral' THEN gp.otherserial
 
WHEN gi.itemtype = 'Computer' THEN info_gc.otherserial
WHEN gi.itemtype = 'Monitor' THEN info_gm.otherserial
WHEN gi.itemtype = 'Peripheral' THEN info_gp.otherserial
WHEN gi.itemtype = 'Printer' THEN info_gpr.otherserial
WHEN gi.itemtype = 'Rack' THEN info_r.otherserial
END AS otherserial,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.serial
WHEN gai.itemtype = 'Monitor' THEN gm.serial
WHEN gai.itemtype = 'Peripheral' THEN gp.serial
 
WHEN gi.itemtype = 'Computer' THEN info_gc.serial
WHEN gi.itemtype = 'Monitor' THEN info_gm.serial
WHEN gi.itemtype = 'Peripheral' THEN info_gp.serial
WHEN gi.itemtype = 'Printer' THEN info_gpr.serial
WHEN gi.itemtype = 'Rack' THEN info_r.serial
END AS serial_num,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.contact
WHEN gai.itemtype = 'Monitor' THEN gm.contact
WHEN gai.itemtype = 'Peripheral' THEN gp.contact
 
WHEN gi.itemtype = 'Computer' THEN info_gc.contact
WHEN gi.itemtype = 'Monitor' THEN info_gm.contact
WHEN gi.itemtype = 'Peripheral' THEN info_gp.contact
WHEN gi.itemtype = 'Printer' THEN info_gpr.contact
END AS contact,

CONCAT(glpi_users.firstname, ' ', glpi_users.realname) AS object_user,
CONCAT(tech_user.firstname, ' ', tech_user.realname) AS tech_user,

glpi_locations.completename as object_location

from glpi_budgets gb 
left join glpi_infocoms gi on gb.id = gi.budgets_id 
left join glpi_appliances ga on ga.id = gi.items_id and gi.itemtype = 'Appliance' 
left join glpi_appliances_items gai on gai.appliances_id = ga.id

left join glpi_computers gc on gc.id = gai.items_id and gai.itemtype = 'Computer'
left join glpi_monitors gm on gm.id = gai.items_id and gai.itemtype = 'Monitor'
left join glpi_peripherals gp on gp.id = gai.items_id and gai.itemtype = 'Peripheral'

left join glpi_computers info_gc on info_gc.id = gi.items_id and gi.itemtype = 'Computer'
left join glpi_monitors info_gm on info_gm.id = gi.items_id and gi.itemtype = 'Monitor'
left join glpi_peripherals info_gp on info_gp.id = gi.items_id and gi.itemtype = 'Peripheral'
left join glpi_printers info_gpr on info_gpr.id = gi.items_id and gi.itemtype = 'Printer'
left join glpi_racks info_r on info_r.id = gi.items_id and gi.itemtype = 'Rack'

left join glpi_budgettypes gbt on gbt.id = gb.budgettypes_id

left join glpi_locations
on (gai.itemtype = 'Computer' and gc.locations_id = glpi_locations.id)
or (gai.itemtype = 'Monitor' and gm.locations_id = glpi_locations.id)
or (gai.itemtype = 'Peripheral' and gp.locations_id = glpi_locations.id)

or (gi.itemtype = 'Computer' and info_gc.locations_id = glpi_locations.id)
or (gi.itemtype = 'Monitor' and info_gm.locations_id = glpi_locations.id)
or (gi.itemtype = 'Peripheral' and info_gp.locations_id = glpi_locations.id)
or (gi.itemtype = 'Printer' and info_gpr.locations_id = glpi_locations.id)
or (gi.itemtype = 'Rack' and info_r.locations_id = glpi_locations.id)

left join glpi_users on
case
when gai.itemtype = 'Computer' then gc.users_id
when gai.itemtype = 'Monitor' then gm.users_id
when gai.itemtype = 'Peripheral' then gp.users_id

when gi.itemtype = 'Computer' then info_gc.users_id
when gi.itemtype = 'Monitor' then info_gm.users_id
when gi.itemtype = 'Peripheral' then info_gp.users_id
when gi.itemtype = 'Printer' then info_gpr.users_id
end = glpi_users.id

left join glpi_users as tech_user on
case
when gai.itemtype = 'Computer' then gc.users_id_tech
when gai.itemtype = 'Monitor' then gm.users_id_tech
when gai.itemtype = 'Peripheral' then gp.users_id_tech

when gi.itemtype = 'Computer' then info_gc.users_id_tech
when gi.itemtype = 'Monitor' then info_gm.users_id_tech
when gi.itemtype = 'Peripheral' then info_gp.users_id_tech
when gi.itemtype = 'Printer' then info_gpr.users_id_tech
when gi.itemtype = 'Rack' then info_r.users_id_tech
end = tech_user.id
".
$dbu->getEntitiesRestrictRequest('WHERE', 'glpi_budgets').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_infocoms').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_computers').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_monitors').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_peripherals').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_printers').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_racks').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_budgettypes').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_locations').
$dbu->getEntitiesRestrictRequest('AND', 'glpi_users').
$dbu->getEntitiesRestrictRequest('AND', 'tech_user').
$report->addSqlCriteriasRestriction().
$report->getOrderBy('budget_type');

$report->setColumns($cols);
$report->setSqlRequest($sql);

$report->execute();   

} else {
   Html::footer();
}

?>
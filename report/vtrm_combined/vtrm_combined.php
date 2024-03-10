<?php
$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

$report = new PluginReportsAutoReport(__('combined_vtrm_report_title', 'reports'));

$budgettype_crit = new PluginReportsBudgetTypeCriteria($report, 'glpi_infocoms.budgets_id');

$report->displayCriteriasForm();

if ($report->criteriasValidated()) {
   $report->setSubNameAuto();

   $cols = [
    new PluginReportsColumn('budget_type', 'Бюджет', [
                                         'sorton' => 'glpi_budgettypes.id']), 
    new PluginReportsColumn('budget_name', 'ВТРМ', [
                                         'sorton' => 'glpi_budgets.id']),
    new PluginReportsColumn('locations_budget_name', 'Местоположение ВТРМ'),
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

  $report->setColumns($cols);

$sql = "
SELECT 
gbt.name as budget_type,
gb.name as budget_name, 
ga.name as appliance_name,
locations_budget.name as locations_budget_name,

CASE
WHEN gai.itemtype = 'Computer' THEN 'Компьютер'
WHEN gai.itemtype = 'Monitor' THEN 'Монитор'
WHEN gai.itemtype = 'Peripheral' THEN 'Периферия'
WHEN gai.itemtype = 'Printer' THEN 'Принтер'
WHEN gai.itemtype = 'Rack' THEN 'Каркас'
WHEN gai.itemtype = 'PassiveDCEquipment' THEN 'Пассивное оборудование постоянного тока'
WHEN gai.itemtype = 'NetworkEquipment' THEN 'Сетевое оборудование'
 
WHEN gi.itemtype = 'Computer' THEN 'Компьютер'
WHEN gi.itemtype = 'Monitor' THEN 'Монитор'
WHEN gi.itemtype = 'Peripheral' THEN 'Периферия'
WHEN gi.itemtype = 'Printer' THEN 'Принтер'
WHEN gi.itemtype = 'Rack' THEN 'Каркас'
WHEN gi.itemtype = 'PassiveDCEquipment' THEN 'Пассивное оборудование постоянного тока'
WHEN gi.itemtype = 'NetworkEquipment' THEN 'Сетевое оборудование'
END AS itemtype_rus,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.name
WHEN gai.itemtype = 'Monitor' THEN gm.name
WHEN gai.itemtype = 'Peripheral' THEN gp.name
WHEN gai.itemtype = 'Printer' THEN gpr.name
WHEN gai.itemtype = 'Rack' THEN r.name
WHEN gai.itemtype = 'PassiveDCEquipment' THEN dc.name
WHEN gai.itemtype = 'NetworkEquipment' THEN neq.name

WHEN gi.itemtype = 'Computer' THEN info_gc.name
WHEN gi.itemtype = 'Monitor' THEN info_gm.name
WHEN gi.itemtype = 'Peripheral' THEN info_gp.name
WHEN gi.itemtype = 'Printer' THEN info_gpr.name
WHEN gi.itemtype = 'Rack' THEN info_r.name
WHEN gi.itemtype = 'PassiveDCEquipment' THEN info_dc.name
WHEN gi.itemtype = 'NetworkEquipment' THEN info_neq.name
END AS object_name,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.date_mod
WHEN gai.itemtype = 'Monitor' THEN gm.date_mod
WHEN gai.itemtype = 'Peripheral' THEN gp.date_mod
WHEN gai.itemtype = 'Printer' THEN gpr.date_mod
WHEN gai.itemtype = 'Rack' THEN r.date_mod
WHEN gai.itemtype = 'PassiveDCEquipment' THEN dc.date_mod
WHEN gai.itemtype = 'NetworkEquipment' THEN neq.date_mod

WHEN gi.itemtype = 'Computer' THEN info_gc.date_mod
WHEN gi.itemtype = 'Monitor' THEN info_gm.date_mod
WHEN gi.itemtype = 'Peripheral' THEN info_gp.date_mod
WHEN gi.itemtype = 'Printer' THEN info_gpr.date_mod
WHEN gi.itemtype = 'Rack' THEN info_r.date_mod
WHEN gi.itemtype = 'PassiveDCEquipment' THEN info_dc.date_mod
WHEN gi.itemtype = 'NetworkEquipment' THEN info_neq.date_mod
END AS date_mod,

-- gai - Объекты комплекса; gi - Объекты ВТРМ
CASE
WHEN gai.itemtype = 'Computer' THEN gc.otherserial
WHEN gai.itemtype = 'Monitor' THEN gm.otherserial
WHEN gai.itemtype = 'Peripheral' THEN gp.otherserial
WHEN gai.itemtype = 'Printer' THEN gpr.otherserial
WHEN gai.itemtype = 'Rack' THEN r.otherserial
WHEN gai.itemtype = 'PassiveDCEquipment' THEN dc.otherserial
WHEN gai.itemtype = 'NetworkEquipment' THEN neq.otherserial
 
WHEN gi.itemtype = 'Computer' THEN info_gc.otherserial
WHEN gi.itemtype = 'Monitor' THEN info_gm.otherserial
WHEN gi.itemtype = 'Peripheral' THEN info_gp.otherserial
WHEN gi.itemtype = 'Printer' THEN info_gpr.otherserial
WHEN gi.itemtype = 'Rack' THEN info_r.otherserial
WHEN gi.itemtype = 'PassiveDCEquipment' THEN info_dc.otherserial
WHEN gi.itemtype = 'NetworkEquipment' THEN info_neq.otherserial
END AS otherserial,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.serial
WHEN gai.itemtype = 'Monitor' THEN gm.serial
WHEN gai.itemtype = 'Peripheral' THEN gp.serial
WHEN gai.itemtype = 'Printer' THEN gpr.serial
WHEN gai.itemtype = 'Rack' THEN r.serial
WHEN gai.itemtype = 'PassiveDCEquipment' THEN dc.serial
WHEN gai.itemtype = 'NetworkEquipment' THEN neq.serial
 
WHEN gi.itemtype = 'Computer' THEN info_gc.serial
WHEN gi.itemtype = 'Monitor' THEN info_gm.serial
WHEN gi.itemtype = 'Peripheral' THEN info_gp.serial
WHEN gi.itemtype = 'Printer' THEN info_gpr.serial
WHEN gi.itemtype = 'Rack' THEN info_r.serial
WHEN gi.itemtype = 'PassiveDCEquipment' THEN info_dc.serial
WHEN gi.itemtype = 'NetworkEquipment' THEN info_neq.serial
END AS serial_num,

CASE
WHEN gai.itemtype = 'Computer' THEN gc.contact
WHEN gai.itemtype = 'Monitor' THEN gm.contact
WHEN gai.itemtype = 'Peripheral' THEN gp.contact
WHEN gai.itemtype = 'Printer' THEN gpr.serial
WHEN gai.itemtype = 'Rack' THEN r.serial
WHEN gai.itemtype = 'NetworkEquipment' THEN neq.contact
 
WHEN gi.itemtype = 'Computer' THEN info_gc.contact
WHEN gi.itemtype = 'Monitor' THEN info_gm.contact
WHEN gi.itemtype = 'Peripheral' THEN info_gp.contact
WHEN gi.itemtype = 'Printer' THEN info_gpr.contact
WHEN gi.itemtype = 'NetworkEquipment' THEN info_neq.contact
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
left join glpi_printers gpr on gpr.id = gai.items_id and gai.itemtype = 'Printer'
left join glpi_racks r on r.id = gai.items_id and gai.itemtype = 'Rack'
left join glpi_networkequipments neq on neq.id = gai.items_id and gai.itemtype = 'NetworkEquipment'
left join glpi_passivedcequipments dc on dc.id = gai.items_id and gai.itemtype = 'PassiveDCEquipment'

left join glpi_computers info_gc on info_gc.id = gi.items_id and gi.itemtype = 'Computer'
left join glpi_monitors info_gm on info_gm.id = gi.items_id and gi.itemtype = 'Monitor'
left join glpi_peripherals info_gp on info_gp.id = gi.items_id and gi.itemtype = 'Peripheral'
left join glpi_printers info_gpr on info_gpr.id = gi.items_id and gi.itemtype = 'Printer'
left join glpi_racks info_r on info_r.id = gi.items_id and gi.itemtype = 'Rack'
left join glpi_networkequipments info_neq on info_neq.id = gi.items_id and gi.itemtype = 'NetworkEquipment'
left join glpi_passivedcequipments info_dc on info_dc.id = gi.items_id and gi.itemtype = 'PassiveDCEquipment'

left join glpi_budgettypes gbt on gbt.id = gb.budgettypes_id

left join glpi_locations
on (gai.itemtype = 'Computer' and gc.locations_id = glpi_locations.id)
or (gai.itemtype = 'Monitor' and gm.locations_id = glpi_locations.id)
or (gai.itemtype = 'Peripheral' and gp.locations_id = glpi_locations.id)
or (gai.itemtype = 'Printer' and gpr.locations_id = glpi_locations.id)
or (gai.itemtype = 'Rack' and r.locations_id = glpi_locations.id)
or (gai.itemtype = 'NetworkEquipment' and neq.locations_id = glpi_locations.id)
or (gai.itemtype = 'PassiveDCEquipment' and dc.locations_id = glpi_locations.id)

or (gi.itemtype = 'Computer' and info_gc.locations_id = glpi_locations.id)
or (gi.itemtype = 'Monitor' and info_gm.locations_id = glpi_locations.id)
or (gi.itemtype = 'Peripheral' and info_gp.locations_id = glpi_locations.id)
or (gi.itemtype = 'Printer' and info_gpr.locations_id = glpi_locations.id)
or (gi.itemtype = 'Rack' and info_r.locations_id = glpi_locations.id)
or (gi.itemtype = 'NetworkEquipment' and info_neq.locations_id = glpi_locations.id)
or (gi.itemtype = 'PassiveDCEquipment' and info_dc.locations_id = glpi_locations.id)

left join glpi_users on
case
when gai.itemtype = 'Computer' then gc.users_id
when gai.itemtype = 'Monitor' then gm.users_id
when gai.itemtype = 'Peripheral' then gp.users_id
when gai.itemtype = 'Printer' then gpr.users_id
when gai.itemtype = 'NetworkEquipment' then neq.users_id

when gi.itemtype = 'Computer' then info_gc.users_id
when gi.itemtype = 'Monitor' then info_gm.users_id
when gi.itemtype = 'Peripheral' then info_gp.users_id
when gi.itemtype = 'Printer' then info_gpr.users_id
when gi.itemtype = 'NetworkEquipment' then info_neq.users_id
end = glpi_users.id

left join glpi_locations locations_budget on locations_budget.id = gb.locations_id

left join glpi_users as tech_user on
case
when gai.itemtype = 'Computer' then gc.users_id_tech
when gai.itemtype = 'Monitor' then gm.users_id_tech
when gai.itemtype = 'Peripheral' then gp.users_id_tech
when gai.itemtype = 'Printer' then gpr.users_id_tech
when gai.itemtype = 'NetworkEquipment' then neq.users_id_tech
when gai.itemtype = 'PassiveDCEquipment' then dc.users_id_tech

when gi.itemtype = 'Computer' then info_gc.users_id_tech
when gi.itemtype = 'Monitor' then info_gm.users_id_tech
when gi.itemtype = 'Peripheral' then info_gp.users_id_tech
when gi.itemtype = 'Printer' then info_gpr.users_id_tech
when gi.itemtype = 'Rack' then info_r.users_id_tech
when gi.itemtype = 'NetworkEquipment' then info_neq.users_id_tech
when gi.itemtype = 'PassiveDCEquipment' then info_dc.users_id_tech
end = tech_user.id
WHERE gi.itemtype IS NOT NULL; 
";

$report->setSqlRequest($sql);
$report->execute(); 

} else {
   Html::footer();
}

?>
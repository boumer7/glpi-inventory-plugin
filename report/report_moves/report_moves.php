<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

//TRANS: The name of the report = Printers
$report = new PluginReportsAutoReport('Отчёт по отсоединениям');

// Definition of the criteria
new PluginReportsDateIntervalCriteria($report, "gl.date_mod");

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {
   $report->setSubNameAuto();

   $cols = [
            # new PluginReportsColumn('linked_action', 'ID действия'),
            new PluginReportsColumnLink('items_id', 'Отсоед. от компьютера', 'Computer',
                                        ['with_navigate' => true]),
            new PluginReportsColumn('per_name', 'Отсоед. устройство'),
            new PluginReportsColumn('otherserial', 'Инвентарный номер отсоед. устройства'),
            new PluginReportsColumn('itemtype_link', 'Тип'),
            new PluginReportsColumn('date_mod', 'Дата отсоединения'),
            new PluginReportsColumnLink('current_pc_id', 'Текущий компьютер', 'Computer',
                                        ['with_navigate' => true])
            # new PluginReportsColumnDateTime('new_value', 'Новое значение')

            // new PluginReportsColumnLink("Here should be value of SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) from sql", 'Тип', 'value of itemtype_link from sql depending on current row')

          ];

   $report->setColumns($cols);

   $sql = "SELECT

          CASE
          WHEN gl.itemtype = 'Computer' THEN 'Компьютер'
          WHEN gl.itemtype = 'Monitor' THEN 'Монитор'
          WHEN gl.itemtype = 'Peripheral' THEN 'Периферия'
          WHEN gl.itemtype = 'Software' THEN 'ПО'
          WHEN gl.itemtype = 'Printer' THEN 'Принтер'
          WHEN gl.itemtype = 'OperatingSystem ' THEN 'Операционная система'  

          END AS itemtype,

          CASE
          WHEN gl.itemtype_link = 'Computer' THEN 'Компьютер'
          WHEN gl.itemtype_link = 'Monitor' THEN 'Монитор'
          WHEN gl.itemtype_link = 'Peripheral' THEN 'Периферия'
          WHEN gl.itemtype_link = 'Software' THEN 'ПО'
          WHEN gl.itemtype_link = 'Printer' THEN 'Принтер'
          WHEN gl.itemtype_link = 'OperatingSystem ' THEN 'Операционная система'  

          END AS itemtype_link,

          CASE
          WHEN gl.itemtype_link = 'Monitor' THEN gm.name
          WHEN gl.itemtype_link = 'Peripheral' THEN gp.name
          WHEN gl.itemtype_link = 'Printer' THEN gpr.name
          WHEN gl.itemtype_link = 'Rack' THEN r.name
          WHEN gl.itemtype_link = 'NetworkEquipment' THEN neq.name
          WHEN gl.itemtype_link = 'OperatingSystem' THEN os.name
          END AS per_name,

          CASE
          WHEN gl.itemtype_link = 'Monitor' THEN gm.otherserial
          WHEN gl.itemtype_link = 'Peripheral' THEN gp.otherserial
          WHEN gl.itemtype_link = 'Printer' THEN gpr.otherserial
          END AS otherserial,

          CASE
          WHEN gl.itemtype_link = 'Monitor' THEN gl.items_id
          WHEN gl.itemtype_link = 'Peripheral' THEN gl.items_id
          WHEN gl.itemtype_link = 'Software' THEN gl.items_id
          WHEN gl.itemtype_link = 'Printer'THEN gl.items_id
          WHEN gl.itemtype_link = 'OperatingSystem' THEN gl.items_id

          END AS device_ids,

          gl.items_id as items_id,
          gl.linked_action as linked_action,
          gl.date_mod as date_mod, 
          gl.old_value as old_value,
          gci.computers_id as current_pc_id,
          SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) as old_value
          -- gl.new_value as new_value

          FROM glpi_logs gl

          LEFT JOIN glpi_monitors gm on gm.id = SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) AND gl.itemtype_link = 'Monitor'

          LEFT JOIN glpi_printers gpr on gpr.id = SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) AND gl.itemtype_link = 'Printer'

          LEFT JOIN glpi_peripherals gp on gp.id = SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) AND gl.itemtype_link = 'Peripheral'

          LEFT JOIN glpi_racks r on r.id = SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) AND gl.itemtype_link = 'Rack'

          LEFT JOIN glpi_operatingsystems os on os.id = SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) AND gl.itemtype_link = 'OperatingSystem'

          left join glpi_networkequipments neq on neq.id = SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) and gl.itemtype_link = 'NetworkEquipment'

          left join glpi_computers_items gci
          on (gl.itemtype_link = 'Monitor' and SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) = gci.items_id and gci.itemtype = 'Monitor')

          or (gl.itemtype_link = 'Peripheral' and SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) = gci.items_id and gci.itemtype = 'Peripheral')

          or (gl.itemtype_link = 'Printer' and SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) = gci.items_id and gci.itemtype = 'Printer')

          or (gl.itemtype_link = 'Software' and SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) = gci.items_id and gci.itemtype = 'Software')

          or (gl.itemtype_link = 'OperatingSystem' and SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) = gci.items_id and gci.itemtype = 'OperatingSystem')

          or (gl.itemtype_link = 'NetworkEquipment' and SUBSTRING_INDEX(SUBSTRING_INDEX(gl.old_value, '(', -1), ')', 1) = gci.items_id and gci.itemtype = 'NetworkEquipment')

          ".
           $dbu->getEntitiesRestrictRequest('WHERE', 'glpi_logs').
           "AND gl.linked_action = 16 AND gl.itemtype = 'Computer' ".
           $report->addSqlCriteriasRestriction();
           $report->getOrderBy('date_mod');

   $report->setSqlRequest($sql);
   $report->execute();

} else {
   Html::footer();
}

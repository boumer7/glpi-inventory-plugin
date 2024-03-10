<?php

$USEDBREPLICATE         = 1;
$DBCONNECTION_REQUIRED  = 0;

include ("../../../../inc/includes.php");

$dbu = new DbUtils();

//TRANS: The name of the report = Printers
$report = new PluginReportsAutoReport(__('printers_report_title', 'reports'));

// Definition of the criteria
$grpcrit = new PluginReportsGroupCriteria($report, 'glpi_printers.groups_id', '', 'is_itemgroup');
$loccrit = new PluginReportsLocationCriteria($report, 'glpi_printers.locations_id');
new PluginReportsDateIntervalCriteria($report, "`glpi_printers`.`date_mod`");

//Display criterias form is needed
$report->displayCriteriasForm();

//If criterias have been validated
if ($report->criteriasValidated()) {
   $report->setSubNameAuto();

   $cols = [new PluginReportsColumnLink('id', __('Name'), 'Printer',
                                        ['with_navigate' => true,
                                         'sorton'        => 'glpi_printers.name']),
            new PluginReportsColumn('state', __('Status')),
            new PluginReportsColumn('manu', __('Manufacturer')),
            new PluginReportsColumn('model', __('Model'),
                                    ['sorton' => 'glpi_manufacturers.name, glpi_printermodels.name']),
            new PluginReportsColumn('serial', __('Serial number')),
            new PluginReportsColumn('otherserial', __('Inventory number')),
            new PluginReportsColumn('immo_number', __('Immobilization number')),
            new PluginReportsColumnDate('buy_date', __('Date of purchase'),
                                        ['sorton' => 'glpi_infocoms.buy_date']),
            new PluginReportsColumnDate('use_date', __('Startup date'),
                                        ['sorton' => 'glpi_infocoms.use_date']),
            new PluginReportsColumnInteger('last_pages_counter', __('Printed pages')),
            new PluginReportsColumnLink('user', __('User'), 'User'),
            new PluginReportsColumnLink('groupe', __('Group'), 'Group',
                                        ['sorton' => 'glpi_groups.name']),
            new PluginReportsColumnInteger('compgrp', __('Computers in the group', 'reports')),
            new PluginReportsColumnInteger('usergrp', __('Users in the group', 'reports')),
            new PluginReportsColumnLink('location', __('Location'), 'Location',
                                        ['sorton' => 'glpi_locations.completename']),
            new PluginReportsColumnInteger('comploc', __('Computers in the location', 'reports')),
            new PluginReportsColumnInteger('userloc', __('Users in the location', 'reports'))];

   $report->setColumns($cols);

   $compgrp = "SELECT COUNT(*)
               FROM `glpi_computers`
               WHERE `glpi_computers`.`groups_id`>0
                     AND `glpi_computers`.`groups_id`=`glpi_printers`.`groups_id`";

   $usergrp = "SELECT COUNT(*)
               FROM `glpi_groups_users`
               WHERE `glpi_groups_users`.`groups_id`>0
                     AND `glpi_groups_users`.`groups_id`=`glpi_printers`.`groups_id`";

   $comploc = "SELECT COUNT(*)
               FROM `glpi_computers`
               WHERE `glpi_computers`.`locations_id`>0
                     AND `glpi_computers`.`locations_id`=`glpi_printers`.`locations_id`";

   $userloc = "SELECT COUNT(*)
               FROM `glpi_users`
               WHERE `glpi_users`.`locations_id`>0
                     AND `glpi_users`.`locations_id`=`glpi_printers`.`locations_id`";

   $sql = "SELECT `glpi_printers`.`id`, `glpi_printers`.`serial`, `glpi_printers`.`otherserial`,
                  `glpi_printers`.`last_pages_counter`,
                  `glpi_printermodels`.`name` AS model,
                  `glpi_manufacturers`.`name` AS manu,
                  `glpi_printers`.`users_id` AS user,
                  `glpi_printers`.`groups_id` AS groupe,
                  (".$compgrp.") AS compgrp,
                  (".$usergrp.") AS usergrp,
                  `glpi_locations`.`id` AS location,
                  (".$comploc.") AS comploc,
                  (".$userloc.") AS userloc,
                  `glpi_infocoms`.`immo_number`, `glpi_infocoms`.`buy_date`,
                  `glpi_infocoms`.`use_date`,
                  `glpi_states`.`name` AS state
           FROM `glpi_printers`
           LEFT JOIN `glpi_printermodels`
               ON (`glpi_printermodels`.`id`=`glpi_printers`.`printermodels_id`)
           LEFT JOIN `glpi_manufacturers`
               ON (`glpi_manufacturers`.`id`=`glpi_printers`.`manufacturers_id`)
           LEFT JOIN `glpi_states` ON (`glpi_states`.`id`=`glpi_printers`.`states_id`)
           LEFT JOIN `glpi_infocoms` ON (`glpi_infocoms`.`itemtype`='Printer'
                                         AND `glpi_infocoms`.`items_id`=`glpi_printers`.`id`)
           LEFT JOIN `glpi_locations` ON (`glpi_locations`.`id`=`glpi_printers`.`locations_id`)
           LEFT JOIN `glpi_groups` ON (`glpi_groups`.`id`=`glpi_printers`.`groups_id`) ".
           $dbu->getEntitiesRestrictRequest('WHERE', 'glpi_printers').
           $report->addSqlCriteriasRestriction().
           $report->getOrderBy('groupe');

   $report->setSqlRequest($sql);
   $report->execute();

} else {
   Html::footer();
}

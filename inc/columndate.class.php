<?php
/**
 -------------------------------------------------------------------------
   LICENSE

 This file is part of Reports plugin for GLPI.

 Reports is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Reports is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero General Public License for more details.

 You should have received a copy of the GNU Affero General Public License
 along with Reports. If not, see <http://www.gnu.org/licenses/>.

 @package   reports
 @authors    Nelly Mahu-Lasson, Remi Collet, Alexandre Delaunay
 @copyright Copyright (c) 2009-2022 Reports plugin team
 @license   AGPL License 3.0 or (at your option) any later version
            http://www.gnu.org/licenses/agpl-3.0-standalone.html
 @link      https://forge.glpi-project.org/projects/reports
 @link      http://www.glpi-project.org/
 @since     2009
 --------------------------------------------------------------------------
 */

/**
 * class PluginReportsColumn to manage output
 */
class PluginReportsColumnDate extends PluginReportsColumn {

   function __construct($name, $title, $options=[]) {

      if (!isset($options['extrafine'])) {
         $options['extrafine'] =  "class='center'";
      }
      if (!isset($options['extrabold'])) {
         $options['extrabold'] =  "class='b center'";
      }

      parent::__construct($name, $title, $options);
   }


   function displayValue($output_type, $row) {

      if (isset($row[$this->name]) && $row[$this->name]) {
         return Html::convDate($row[$this->name]);
      }
      return '';
   }
}

<?php

class PluginReportsBudgetTypeCriteria extends PluginReportsDropdownCriteria {


   /**
    * @param $report
    * @param $name 
    * @param $label
   **/
   function __construct($report, $name='budgets_id', $label='') {

      parent::__construct($report, $name, 'glpi_budgets', ($label ? $label : ('Бюджет')));
   }


   /**
    * @param $budget_type
   **/
   public function setDefaultBudgetType($budget_type) {
      $this->addParameter($this->id, $budget_type);
   }

}

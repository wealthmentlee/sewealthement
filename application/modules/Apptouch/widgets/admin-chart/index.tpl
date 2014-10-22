<?php
$columns = null;
$rows = Zend_Json::encode($this->data['rows']);
foreach($this->data['columns'] as $column){
  $columns.="data.addColumn('".$column[0]."', '".$column[1]."');
      ";
}
if($columns == null || $rows == null){
  echo $this->translate('TOUCH_Chart:Supplied data is not correctly formatted!');
} elseif(!$this->type){
  echo $this->translate('TOUCH_Chart:Unknown Chart Type or type is not defined!');
} elseif(!$this->options || empty($this->options)){
  echo $this->translate('TOUCH_Chart:No options were supplied!');
} else{
$this->headScript()
   ->appendFile('https://www.google.com/jsapi')
  ->appendScript("
  // Load the Visualization API and the piechart package.
        google.load('visualization', '1.0', {'packages':['corechart']});

        // Set a callback to run when the Google Visualization API is loaded.
        google.setOnLoadCallback(drawChart);
      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        ".$columns."
        data.addRows(".$rows.");

        // Set chart options
        var options = ".$this->options.";

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.".$this->type."(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
  ");
  if($this->debug){
    print_arr($columns);
    print_arr($rows);
    print_arr($this->options);
    print_arr($this->type);
  }
?>
<div id="chart_div">

</div>
  <?php
  }
   ?>


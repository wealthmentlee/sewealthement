<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 16.03.12
 * Time: 17:40
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Widget_AdminChartController extends Engine_Content_Widget_Abstract
{
  private $chart_types = array(
    'pie' => 'PieChart',
    'area' => 'AreaChart',
    'bar' =>'BarChart',
    'bubble' => 'BubbleChart',
    'candlestick' => 'CandlestickChart',
    'column' => 'ColumnChart',
    'combo' => 'ComboChart',
    'gauge'  => 'GaugeChart',
    'geo' => 'GeoChart',
    'line' => 'LineChart',
    'pie' => 'PieChart',
    'scatter' => 'ScatterChart',
    'stepped-area' => 'SteppedAreaChart',
    'table' => 'TableChart',
    'tree-map' => 'TreeMapChart'
  );
  private $chart_params = null;
  public function indexAction(){
    $this->view->chartTypes = $this->chart_types;
    $this->chart_params = $this->_getParam('chart_params');

    if($this->chart_params == null){
      return $this->setNoRender();
    }
    $this->view->type = $this->chart_types[$this->chart_params['type']];
    $this->view->options = "JSON.decode('".Zend_Json::encode($this->chart_params['options'])."')";
    $this->view->data = $this->chart_params['data'];
//    $this->view->debug = true;
  }
}

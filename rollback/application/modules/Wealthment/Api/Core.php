<?php

class Wealthment_Api_Core extends Core_Api_Abstract
{
    
    public function getCatLabel($cat_id) {
        $array = array(
            '0' => 'All',  
			'1' => 'Stocks',  
            '2' => 'Real Estate',
            '3' => 'Retirement',
            '4' => 'Other Savings',
        );
        
        return $array[$cat_id];
    }
    
}

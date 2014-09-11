<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 20.06.12
 * Time: 17:21
 * To change this template use File | Settings | File Templates.
 */
class Like_Widget_MostLikedArticlesController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $advarticle = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedarticles');
        $article = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('article');
        if (!$article && !$advarticle) 
        {
            $this->setNoRender($this);
            return;
        }

        $this->view->widget = 'most_liked';
        
        $this->view->item_type = $item_type = $article 
                ? 'article'
                : 'artarticle';
        
        
        $api = Engine_Api::_()->getApi('core', 'like');

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $ipp = $settings->getSetting('like.article_count', 9);
        $this->view->period = $period = $settings->getSetting('like.article_period', 1);

        $data = $api->getMostLiked($item_type, $ipp);
        if (!$data) 
        {
            $this->setNoRender();
            return ;
        }

        if ($period) 
        {
            $week_paginator = $api->getMostLiked($item_type, $ipp, 'week');
            $this->view->week_likes = $week_likes = $week_paginator['paginator'];
            $this->view->week_counts = $week_paginator['counts'];
            if ($week_likes->getTotalItemCount()) 
            {
                $this->view->week_likes->setItemCountPerPage($ipp);
            }

            $month_paginator = $api->getMostLiked($item_type, $ipp, 'month');
            $this->view->month_likes = $month_likes = $month_paginator['paginator'];
            $this->view->month_counts = $month_paginator['counts'];
            if ($month_likes->getTotalItemCount()) 
            {
                $this->view->month_likes->setItemCountPerPage($ipp);
            }
        }
        $this->view->all_likes = $all_likes = $data['paginator'];
        if (!$all_likes->getTotalItemCount() && $all_likes) 
        {
            $this->setNoRender();
            return ;
        }
        $this->view->all_likes->setItemCountPerPage($ipp);
        $this->view->all_counts = $data['counts'];

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);

        $this->getElement()->setAttrib('class', 'like_widget_theme_' . $this->view->activeTheme());
    }
}

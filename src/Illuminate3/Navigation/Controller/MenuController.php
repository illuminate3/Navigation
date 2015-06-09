<?php

namespace Illuminate3\Navigation\Controller;

use Illuminate3\Navigation\Model\Container;
use Illuminate3\Navigation\Model\Node;
use Illuminate3\Pages\Model\Page;
use Config, App, Route;

class MenuController extends \BaseController
{
    protected $menu;
    
    public function container($container, $class = 'nav navbar-nav')
    {
        $this->menu = App::make('Menu\Menu')->handler($container, compact('class'));
                
        $nodes = Node::getChildrenByRouteAndContainer(Route::currentRouteName(), $container);
              
        if(!$nodes->count()) {
            return;
        }
        
        foreach($nodes as $node) {
            $this->buildMenu($node);
        }
        
        return $this->menu->render();
    }
    
    public function buildMenu($node)
    {
        $linkAttribs = array(
			'class' => $node->link_class,
			'role' => 'button',
		);
        $listAttribs = array();
        $title = $node->title;
        $sub = null;
        
        if($node->children->count()) {
            $sub = App::make('Menu\Menu')->items('test', array('class' => 'dropdown-menu'));
            foreach($node->children as $child) {

                if($child->page) {
                    $sub->add(url($child->page->route), $child->title);
                }
                else {
                    $sub->add('', $child->title);
                }
            }
            $title = $node->title .= ' <b class="caret"></b>';
            $linkAttribs = array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
            );
            $listAttribs = array(
                'class' => 'dropdown',
            );
        }
        
        if($node->page) {
            $this->menu->add(url($node->page->route), $title, $sub, $linkAttribs, $listAttribs);
        }
        else {
            $this->menu->add('', $title, $sub, $linkAttribs, $listAttribs);
        }
    }

}
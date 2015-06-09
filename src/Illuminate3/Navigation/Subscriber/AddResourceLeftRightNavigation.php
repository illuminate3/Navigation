<?php

namespace Illuminate3\Navigation\Subscriber;

use Illuminate\Events\Dispatcher as Events;
use Illuminate3\Admin\Model\Resource;
use Illuminate3\Navigation\Model\Node;
use Illuminate3\Pages\Model\Page;

/**
 * Each time a resource page is created, we add the appropriate navigation on that page.
 */
class AddResourceLeftRightNavigation
{
	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param Events $events
	 */
	public function subscribe(Events $events)
	{
		$events->listen('page.createResourcePages', array($this, 'onCreatedResourcePages'));
	}

	/**
	 * @param Resource $resource
	 * @param array    $pages
	 */
	public function onCreatedResourcePages(Array $pages) {

		$index 		= $pages['index'];
		$create 	= $pages['create'];
		$edit 		= $pages['edit'];
		$destroy 	= $pages['destroy'];

		foreach($pages as $action => $page) {

			// Left menu root node
			$left = Node::create(array(
				'title' => $page->title,
				'page_id' => $page->id,
				'container_id' => \NavigationContainersTableSeeder::LEFT,
			));

			// Right menu root node
			$right = Node::create(array(
				'title' => $page->title,
				'page_id' => $page->id,
				'container_id' => \NavigationContainersTableSeeder::RIGHT,
			));

			$base = substr($page->alias, 0, strrpos($page->alias, '.'));

			switch($action) {

				case 'index':

					$left->children()->create(array(
						'title' => 'Dashboard',
						'page_id' => Page::whereAlias('admin.index')->first()->id,
						'link_class' => 'btn btn-default',
					));

					$right->children()->create(array(
						'title' => $create->title,
						'page_id' => $create->id,
						'link_class' => 'btn btn-primary',
					));

					break;

				case 'create':

					$left->children()->create(array(
						'title' => $index->title,
						'page_id' => $index->id,
						'link_class' => 'btn btn-default',
					));

					break;

				case 'edit':

					$left->children()->create(array(
						'title' => $index->title,
						'page_id' => $index->id,
						'link_class' => 'btn btn-default',
					));

					// Delete
					$right->children()->create(array(
						'title' => $destroy->title,
						'page_id' => $destroy->id,
						'link_class' => 'btn btn-danger',
					));

					break;
			}

		}
	}


}
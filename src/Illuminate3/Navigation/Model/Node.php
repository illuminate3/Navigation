<?php

namespace Illuminate3\Navigation\Model;

use Illuminate\Database\Eloquent\Collection;

class Node extends \Baum\Node
{

    protected $table = 'navigation_nodes';

    public $timestamps = false;

    public $rules = array();

    protected $guarded = array('id');

    protected $fillable = array(
        'title',
        'description',
        'container_id',
        'page_id',
        'params',
        );

    /**
     * @return Container
     */
    public function container()
    {
        return $this->belongsTo('Illuminate3\Navigation\Model\Container');
    }

    /**
     * @return \Illuminate3\Pages\Model\Page
     */
    public function page()
    {
            return $this->belongsTo('Illuminate3\Pages\Model\Page');
    }


	public function getParamsAttribute($value)
	{
		if(!$value) {
			return array();
		}

		return unserialize($value);
	}

	public function setParamsAttribute(Array $value = array())
	{
		$this->attributes['params'] = serialize($value);
	}

	/**
	 *
	 * @param string $containerName
	 * @return Illuminate\Database\Eloquent\Builder
	 */
	static public function getChildrenByContainerQuery($containerName)
	{
		$qb = self::query();
		$qb->join('navigation_containers', 'navigation_nodes.container_id', '=', 'navigation_containers.id')
			->where('navigation_containers.name', '=', $containerName)
			->select('navigation_nodes.*');

		return $qb->with('page');
	}

	/**
	 *
	 * @param string $containerName
	 * @return Node
	 */
	static public function getChildrenByContainer($containerName)
	{
		return static::getChildrenByContainerQuery($containerName)->get();
	}

    /**
     * 
     * @param type $routeName
     * @param type $containerName
     * @return Node
     */
    static public function findRootByRouteAndContainer($routeName, $containerName)
    {        
        $qb = self::query();
        $qb->join('pages', 'navigation_nodes.page_id', '=', 'pages.id')
           ->join('navigation_containers', 'navigation_nodes.container_id', '=', 'navigation_containers.id')
           ->where('navigation_containers.name', '=', $containerName)
           ->where('pages.alias', '=', $routeName)
           ->select('navigation_nodes.*');
        
        return $qb->first();
    }
    
    /**
     * 
     * @param type $routeName
     * @param type $containerName
     * @return Collection
     */
    static public function getChildrenByRouteAndContainer($routeName, $containerName)
    {
        $node = self::findRootByRouteAndContainer($routeName, $containerName);
        
        if(!$node) {
            return new Collection;
        }
        
        return $node->children()->get();
    }
}


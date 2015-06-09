<?php

namespace Illuminate3\Navigation\Model;

class Container extends \Eloquent
{

    protected $table = 'navigation_containers';

    public $timestamps = false;

    public $rules = array();

    protected $guarded = array('id');

    protected $fillable = array(
        'title',
        'name'
        );


}


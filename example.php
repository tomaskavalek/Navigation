<?php
/*
 .
 .
 .
*/

protected function createComponentNavigation($name) {
	$navigation = new Navigation($this, $name);
	
	$navigation->setupHomepage("Hlavní stránka", $this->link("Default:default"));
	
	// Nacteni polozek pres DISTINCT a LEFT JOIN tak, aby pro vytvareni navigace
	// pomoci $menu[$item->id] = $menu[$item->parent]->add(...) bylo zajisteno,
	// ze $menu[$item->parent], resp. index $item->parent, existuje.
	$items = $this->db->select('menu1.*')
		->setFlag('distinct')
		->from('menu')->as('menu1')
		->leftJoin('menu')->as('menu2')
		->on('menu1.id = menu2.parent')
		->orderBy('menu1.`parent`, menu1.`order`');
				
	$menu[0] = $navigation;
	
	foreach($items as $item) {
		if(!is_null($item->params)) $link = $this->link($item->link, json_decode($item->params, true));
		else $link = $this->link($item->link);
		
		$menu[$item->id] = $menu[$item->parent]->add($item->name, $link);
		if(Environment::getHttpRequest()->getUri()->isEqual($link)) $current = $menu[$item->id];
	}
	
	if(isset($current)) $navigation->setCurrent($current);
}

/*
 .
 .
 .
*/
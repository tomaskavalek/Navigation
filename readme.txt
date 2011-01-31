Navigation
==========

Control pro Nette Framework usnadňující tvorbu menu a drobečkové navigace

Autor: Jan Marek
Licence: MIT

Použití
-------

Továrnička v presenteru:

	protected function createComponentNavigation($name) {
		$nav = new Navigation($this, $name);
		$nav->setupHomepage("Úvod", $this->link("Homepage:"));
		$sec = $nav->add("Sekce", $this->link("Category:", array("id" => 1)));
		$article = $sec->add("Článek", $this->link("Article:", array("id" => 1)));
		$nav->setCurrent($article);
	}


Menu v šabloně:

	{widget navigation}


Drobečková navigace v šabloně:

	{widget navigation:breadcrumbs}

--------------------------------------------------------------------------------

Autor: Tomáš Kavalek
	
Jednoduché použití při práci s databází
---------------------------------------

Příklad jednoduché tabulky:

+---------+------------------------------------+-------------------------------+ 
| Sloupec | Typ	                               | Komentář                      |
| id      | tinyint(3) unsigned Auto Increment | PK                            |
| parent  | tinyint(3) unsigned Default 0      | rodičovský prvek              |
| name    | varchar(255)                       | titulek odkazu                |
| link    | varchar(255)                       | odkaz na presenter a action   |
| params  | varchar(255) NULL                  | případné parametry v JSON     |
+---------+------------------------------------+-------------------------------+

id	parent	name		link					params
1	0		Oddělení	Default:showPage		{"id":1}
2	0		Kontakt		Default:showContact		NULL
3	1		IT			Default:showPage		{"id":2}
4	1		Ekonomické	Default:showPage		{"id":3}
5	3		Podpora		Default:showPage		{"id":4}
6	3		Prodej		Default:showSitemap		{"id":5}

Továrnička v presenteru:

protected function createComponentNavigation($name) {
	// Načtení z DB je lepší mít v modelu
	// Do DB lze vhodně přiřadit další atributy, např. pořadí (order)
	$items = $this->db->select('`menu`.*')
		->from('`menu`')
		->orderBy('`menu`.`id`')
		->fetchAll();
	
	
	$navigation = new Navigation($this, $name);
	
	// Hlavní stránka je definována pevně v presenteru
	$navigation->setupHomepage("Hlavní stránka", $this->link("Default:default"));
	
	// Root
	$menu[0] = $navigation;
	
	foreach($items as $item) {
		// Vytvoření odkazu - s parametry / bez parametrů
		if(!is_null($item->params)) $link = $this->link($item->link, json_decode($item->params, true));
		else $link = $this->link($item->link);
		
		// Vytvoření položky navigace
		$menu[$item->id] = $menu[$item->parent]->add($item->name, $link);
		
		// Je položka navigace aktivní?
		if(Environment::getHttpRequest()->getUri()->isEqual($link)) $current = $menu[$item->id];
	}
	
	// Nastavení aktivní položky
	if(isset($current)) $navigation->setCurrent($current);
}
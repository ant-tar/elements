<?php
/** 
 * This plugin handle 301 redirect for news and courses
 * 
 * DESCRIPTION
 * Each item to be opened by a url, i.e. http://ikb.mangoconsulting.co.uk/sbl-sixth/making-the-right-choices would take the user to: http://ikb.mangoconsulting.co.uk/sbl-sixth#making-the-right-choices. It also concerns pages from http://ikb.mangoconsulting.co.uk/news-events/ container
 *
 * IMPORTANT
 * As prerequisites we agree that News container(News/Events) has ID = 4 and Courses container (SBL Sixth) has ID = 21. If you'll change tree structure be aware of ID's conformity
 * 
 * @author Anton Tarasov <contact@antontarasov.com>
 * 
**/

define("NEWS_CONTAINER_ID", 4);
define("COURSES_CONTAINER_ID", 21);

$eventName = $modx->event->name;
switch($eventName) {
	case 'OnLoadWebDocument':
		$uri = ltrim($_SERVER['REQUEST_URI'],'/');
		if(!empty($uri)){ // deeper than homepage
			$u_parts = explode('/', $uri);
			if(isset($u_parts[1]) && !empty($u_parts[1])){ // deep level like news or courses have
				// firstly we have to check a fact that called news or course page are exist
				$c = $modx->newQuery('modResource');
				$where = array(
							array(
								'alias'	=> $u_parts[1],
							),
							array(
								'parent' => COURSES_CONTAINER_ID,
								'OR:parent:=' => NEWS_CONTAINER_ID,
							),
						);
				$c->where($where);
				$c->select(
						array(
							'modResource.alias',
							));
				$c->prepare(); 
				$c->stmt->execute();
				$parent = $c->stmt->fetch(PDO::FETCH_ASSOC);
				if(!empty($parent)){  //if exists we can redirect to parent container with appropriate fragment
					$path = $modx->getOption('site_url');
					$path .= $u_parts[0]."#".$u_parts[1];
					$options = array('responseCode' => 'HTTP/1.1 301 Moved Permanently');
					$modx->sendRedirect($path,$options);
				}
			}
		}
		break;
	default:
		break;
}

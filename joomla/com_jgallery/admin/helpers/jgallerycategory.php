<?php
use Joomla\CMS\Categories\Categories as JCategories;
use Joomla\CMS\Factory as JFactory;

JLoader::import('components.com_jgallery.models.categories', JPATH_ADMINISTRATOR);

class JGalleryCategories extends JCategories
{
	public function __construct($options = array())
	{
		$options['table'] = '#__content';
		$options['extension'] = 'com_jgallery';
		$options['statefield'] = 'published';
		parent::__construct($options);
	}
}



abstract class JGalleryCategoryHelper 
{
	static function getcategorytitle($catID) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT title FROM #__categories WHERE id = ".$catID);
		$title = $db->loadResult();
		return $title;
	}

	static function getcategoryaccess($catID) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT access FROM #__categories WHERE id = ".$catID);
		$access = $db->loadResult();
		return $access;
	}


	static function getcategoryparams($catID) {
		$jmodelcategories = JGalleryModelCategories::getInstance();
		$category = $jmodelcategories->getItem($catID);
		return json_decode($category->params);
	}


	static function getcategoryandchildren($catID, $type='Content', $recurse=true) {
		$categories = JCategories::getInstance($type);
		$cat = $categories->get($catID);
		$catchildren = array($catID);
		if ($cat != null) {
			$children = $cat->getChildren();
			foreach ($children as $child) {
				$catchildren = array_merge($catchildren, JGalleryCategoryHelper::getcategoryandchildren($child->id, $type, true));
			}
		}
		return $catchildren;
	}


	static function usercanwritecategory($user, $category, $writeaccess=false)
	{
		$jmodelcategories = JGalleryModelCategories::getInstance();
		return $jmodelcategories->usercanwritecategory($user, $category);
	}


	static function usercanreadcategory($user, $category)
	{
		$jmodelcategories = JGalleryModelCategories::getInstance();
		return $jmodelcategories->usercanreadcategory($user, $category);
	}

	static function usercanviewcategory($user, $catid)
	{
		if ($catid == null) { 
			$ok = true;
		} else {
			$levels = $user->getAuthorisedViewLevels();
			$access = self::getcategoryaccess($catid);
			$ok = in_array($access, $levels);
		}
		return $ok;
	}

}
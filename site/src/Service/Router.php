<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Ra_walks_editor
 * @author     Chris Vaughan  <ruby.tuesday@ramblers-webs.org.uk>
 * @copyright  2024 ruby.tuesday@ramblers-webs.org.uk
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Ramblers\Component\Ra_walks_editor\Site\Service;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Categories\Categories;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Menu\AbstractMenu;

/**
 * Class Ra_walks_editorRouter
 *
 */
class Router extends RouterView
{
	private $noIDs;
	/**
	 * The category factory
	 *
	 * @var    CategoryFactoryInterface
	 *
	 * @since  1.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var    array
	 *
	 * @since  1.0.0
	 */
	private $categoryCache = [];

	public function __construct(SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db)
	{
		$params = Factory::getApplication()->getParams('com_ra_walks_editor');
		$this->noIDs = (bool) $params->get('sef_ids');
		$this->categoryFactory = $categoryFactory;
		
		
			$walks = new RouterViewConfiguration('walks');
		$walks->setKey('catid')->setNestable();
			$this->registerView($walks);
			$ccWalk = new RouterViewConfiguration('walk');
			$ccWalk->setKey('id')->setParent($walks, 'catid');
			$this->registerView($ccWalk);
			$walkform = new RouterViewConfiguration('walkform');
			$walkform->setKey('id');
			$this->registerView($walkform);
			$places = new RouterViewConfiguration('places');
			$this->registerView($places);
			$ccPlace = new RouterViewConfiguration('place');
			$ccPlace->setKey('id')->setParent($places);
			$this->registerView($ccPlace);
			$placeform = new RouterViewConfiguration('placeform');
			$placeform->setKey('id');
			$this->registerView($placeform);
			$contacts = new RouterViewConfiguration('contacts');
			$this->registerView($contacts);
			$ccContact = new RouterViewConfiguration('contact');
			$ccContact->setKey('id')->setParent($contacts);
			$this->registerView($ccContact);
			$contactform = new RouterViewConfiguration('contactform');
			$contactform->setKey('id');
			$this->registerView($contactform);
			$grades = new RouterViewConfiguration('grades');
			$this->registerView($grades);
			$ccGrade = new RouterViewConfiguration('grade');
			$ccGrade->setKey('id')->setParent($grades);
			$this->registerView($ccGrade);
			$gradeform = new RouterViewConfiguration('gradeform');
			$gradeform->setKey('id');
			$this->registerView($gradeform);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}


	
			/**
			 * Method to get the segment(s) for a category
			 *
			 * @param   string  $id     ID of the category to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getWalksSegment($id, $query)
			{
				$category = $this->getCategories(["access" => true])->get($id);

				if ($category)
				{
					$path = array_reverse($category->getPath(), true);
					$path[0] = '1:root';

					if ($this->noIDs)
					{
						foreach ($path as &$segment)
						{
							list($id, $segment) = explode(':', $segment, 2);
						}
					}

					return $path;
				}

				return array();
			}
		/**
		 * Method to get the segment(s) for an walk
		 *
		 * @param   string  $id     ID of the walk to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getWalkSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an walkform
			 *
			 * @param   string  $id     ID of the walkform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getWalkformSegment($id, $query)
			{
				return $this->getWalkSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an place
		 *
		 * @param   string  $id     ID of the place to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getPlaceSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an placeform
			 *
			 * @param   string  $id     ID of the placeform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getPlaceformSegment($id, $query)
			{
				return $this->getPlaceSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an contact
		 *
		 * @param   string  $id     ID of the contact to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getContactSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an contactform
			 *
			 * @param   string  $id     ID of the contactform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getContactformSegment($id, $query)
			{
				return $this->getContactSegment($id, $query);
			}
		/**
		 * Method to get the segment(s) for an grade
		 *
		 * @param   string  $id     ID of the grade to retrieve the segments for
		 * @param   array   $query  The request that is built right now
		 *
		 * @return  array|string  The segments of this item
		 */
		public function getGradeSegment($id, $query)
		{
			return array((int) $id => $id);
		}
			/**
			 * Method to get the segment(s) for an gradeform
			 *
			 * @param   string  $id     ID of the gradeform to retrieve the segments for
			 * @param   array   $query  The request that is built right now
			 *
			 * @return  array|string  The segments of this item
			 */
			public function getGradeformSegment($id, $query)
			{
				return $this->getGradeSegment($id, $query);
			}

	
			/**
			 * Method to get the id for a category
			 *
			 * @param   string  $segment  Segment to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getWalksId($segment, $query)
			{
				if (isset($query['catid']))
				{
					$category = $this->getCategories(["access" => true])->get($query['catid']);

					if ($category)
					{
						foreach ($category->getChildren() as $child)
						{
							if ($this->noIDs)
							{
								if ($child->alias == $segment)
								{
									return $child->id;
								}
							}
							else
							{
								if ($child->id == (int) $segment)
								{
									return $child->id;
								}
							}
						}
					}
				}

				return false;
			}
		/**
		 * Method to get the segment(s) for an walk
		 *
		 * @param   string  $segment  Segment of the walk to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getWalkId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an walkform
			 *
			 * @param   string  $segment  Segment of the walkform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getWalkformId($segment, $query)
			{
				return $this->getWalkId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an place
		 *
		 * @param   string  $segment  Segment of the place to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getPlaceId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an placeform
			 *
			 * @param   string  $segment  Segment of the placeform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getPlaceformId($segment, $query)
			{
				return $this->getPlaceId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an contact
		 *
		 * @param   string  $segment  Segment of the contact to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getContactId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an contactform
			 *
			 * @param   string  $segment  Segment of the contactform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getContactformId($segment, $query)
			{
				return $this->getContactId($segment, $query);
			}
		/**
		 * Method to get the segment(s) for an grade
		 *
		 * @param   string  $segment  Segment of the grade to retrieve the ID for
		 * @param   array   $query    The request that is parsed right now
		 *
		 * @return  mixed   The id of this item or false
		 */
		public function getGradeId($segment, $query)
		{
			return (int) $segment;
		}
			/**
			 * Method to get the segment(s) for an gradeform
			 *
			 * @param   string  $segment  Segment of the gradeform to retrieve the ID for
			 * @param   array   $query    The request that is parsed right now
			 *
			 * @return  mixed   The id of this item or false
			 */
			public function getGradeformId($segment, $query)
			{
				return $this->getGradeId($segment, $query);
			}

	/**
	 * Method to get categories from cache
	 *
	 * @param   array  $options   The options for retrieving categories
	 *
	 * @return  CategoryInterface  The object containing categories
	 *
	 * @since   1.0.0
	 */
	private function getCategories(array $options = []): CategoryInterface
	{
		$key = serialize($options);

		if (!isset($this->categoryCache[$key]))
		{
			$this->categoryCache[$key] = $this->categoryFactory->createCategory($options);
		}

		return $this->categoryCache[$key];
	}
}

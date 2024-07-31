<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ra_walks_editor
 * @author     Chris Vaughan  <ruby.tuesday@ramblers-webs.org.uk>
 * @copyright  2024 ruby.tuesday@ramblers-webs.org.uk
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Ramblers\Component\Ra_walks_editor\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;
use Ramblers\Component\Ra_walks_editor\Administrator\Helper\Ra_walks_editorHelper;

/**
 * Methods supporting a list of Walks records.
 *
 * @since  1.0.0
 */
class WalksModel extends ListModel
{
	/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'date', 'a.date',
		'date.from', 'date.to',
				'id', 'a.id',
				'category', 'a.category',
				'content', 'a.content',
				'status', 'a.status',
			);
		}

		parent::__construct($config);
	}


	

	

	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('category', 'ASC');

		$context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $context);

		// Split context into component and optional section
		if (!empty($context))
		{
			$parts = FieldsHelper::extract($context);

			if ($parts)
			{
				$this->setState('filter.component', $parts[0]);
				$this->setState('filter.section', $parts[1]);
			}
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		
		return parent::getStoreId($id);
		
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   1.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__ra_walks_editor_walks` AS a');
		
		// Join over the users for the checked out user
		$query->select("uc.name AS uEditor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif (empty($published))
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.status LIKE ' . $search . ' )');
			}
		}
		

		// Filtering date
		$filter_date_from = $this->state->get("filter.date.from");

		if ($filter_date_from !== null && !empty($filter_date_from))
		{
			$query->where("a.`date` >= '".$db->escape($filter_date_from)."'");
		}
		$filter_date_to = $this->state->get("filter.date.to");

		if ($filter_date_to !== null  && !empty($filter_date_to))
		{
			$query->where("a.`date` <= '".$db->escape($filter_date_to)."'");
		}

		// Filtering category
		$filter_category = $this->state->get("filter.category");

		if ($filter_category !== null && !empty($filter_category))
		{
			$query->where("a.`category` = '".$db->escape($filter_category)."'");
		}

		// Filtering status
		$filter_status = $this->state->get("filter.status");

		if ($filter_status !== null && (is_numeric($filter_status) || !empty($filter_status)))
		{
			$query->where("a.`status` = '".$db->escape($filter_status)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'category');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		
		foreach ($items as $oneItem)
		{

			if (isset($oneItem->category))
			{
				$db    = $this->getDbo();
				$query = $db->getQuery(true);

				$query
					->select($db->quoteName('title'))
					->from($db->quoteName('#__categories'))
					->where('FIND_IN_SET(' . $db->quoteName('id') . ', ' . $db->quote($oneItem->category) . ')');

				$db->setQuery($query);
				$result = $db->loadColumn();

				$oneItem->category = !empty($result) ? implode(', ', $result) : '';
			}
					$oneItem->status = !empty($oneItem->status) ? Text::_('COM_RA_WALKS_EDITOR_WALKS_STATUS_OPTION_' . preg_replace('/[^A-Za-z0-9\_-]/', '',strtoupper(str_replace(' ', '_',$oneItem->status)))) : '';
		}

		return $items;
	}
}

<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Onecard records.
 *
 * @since  1.6
 */
class OnecardModelCodes extends JModelList
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
				'id','a.`id`',
'state','a.`state`',
'ordering','a.`ordering`',
'created_by','a.`created_by`',
'modified_by','a.`modified_by`',
'code','a.`code`',
'barcode','a.`barcode`',
'expired','a.`expired`',
'voucher','a.`voucher`',
'status','a.`status`',

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
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		
		
		
		// Filtering created_by
		$this->setState('filter.created_by', $app->getUserStateFromRequest($this->context.'.filter.created_by', 'filter_created_by', '', 'string'));
		

		// Filtering voucher
		$this->setState('filter.voucher', $app->getUserStateFromRequest($this->context.'.filter.voucher', 'filter_voucher', '', 'string'));
		

		// Filtering status
		$this->setState('filter.status', $app->getUserStateFromRequest($this->context.'.filter.status', 'filter_status', '', 'string'));
		


		// Load the parameters.
		$params = JComponentHelper::getParams('com_onecard');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.id', 'asc');
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
	 * @return   string A store id.
	 *
	 * @since    1.6
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
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
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
		$query->from('`#__onecard_code` AS a');

		// Join over the users for the checked out user
		$query->select("uc.name AS editor");
		$query->join("LEFT", "#__users AS uc ON uc.id=a.checked_out");


		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Join over the user field 'modified_by'
		$query->select('`modified_by`.name AS `modified_by`');
		$query->join('LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`');
		
		
		
		
		
		
		//Filtering created_by
		$filter_created_by = $this->state->get("filter.created_by");
		if ($filter_created_by)
		{
			$query->where("a.`created_by` = '".$db->escape($filter_created_by)."'");
		}

		


		//Filtering voucher
		$filter_voucher = $this->state->get("filter.voucher");
		if ($filter_voucher)
		{
			$query->where("a.`voucher` = '".$db->escape($filter_voucher)."'");
		}

		


		//Filtering status
		$filter_status = $this->state->get("filter.status");
		if ($filter_status)
		{
			$query->where("a.`status` = '".$db->escape($filter_status)."'");
		}

		


		
		
		
		
		
		
		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
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
				
				$query->where("
 a.`id` like $search  || 
 a.`state` like $search  || 
 a.`ordering` like $search  || 
 a.`created_by` like $search  || 
 a.`modified_by` like $search  || 
 a.`code` like $search  || 
 a.`barcode` like $search  || 
 a.`expired` like $search  || 
 a.`voucher` like $search  || 
 a.`status` like $search 
");
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

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
		
		foreach ($items as $oneItem) {
			
			
			if (isset($oneItem->voucher)) {
				$values = explode(',', $oneItem->voucher);

				$textValue = array();
				foreach ($values as $value){
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query
							->select($db->quoteName('title'))
							->from('`#__onecard_voucher`')
							->where($db->quoteName('id') . ' = '. $db->quote($db->escape($value)));
					$db->setQuery($query);
					$results = $db->loadObject();
					if ($results) {
						$textValue[] = $results->title;
					}
				}

			$oneItem->voucher = !empty($textValue) ? implode(', ', $textValue) : $oneItem->voucher;

			}
		


			
		}

		return $items;
	}
}

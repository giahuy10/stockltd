<?php
/**
  * @version    1.0.0
  * @package    com_onecard
  * @author     Not Set <Not Set>
  * @copyright  No copyright
  * @license    GNU General Public License version 2 or later; see LICENSE.txt
  */
// No direct access
defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
/**
 * export_voucher Table class
 *
 * @since  1.6
 */
class OnecardTableExport_voucher extends JTable
{
    
    /**
     * Constructor
     *
     * @param   JDatabase  &$db  A database connector object
     */
    public function __construct(&$db)
    {
        JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'OnecardTableexport_voucher', array('typeAlias' => 'com_onecard.export_voucher'));
        parent::__construct('#__onecard_export_voucher', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  Optional array or list of parameters to ignore
     *
     * @return  null|string  null is operation was satisfactory, otherwise returns an error
     *
     * @see     JTable:bind
     * @since   1.5
     */
    public function bind($array, $ignore = '')
    {
        $input = JFactory::getApplication()->input;
        $task = $input->getString('task', '');

        if ($array['id'] == 0)
        {
            $array['created_by'] = JFactory::getUser()->id;
        }

        if (isset($array['params']) && is_array($array['params']))
        {
            $registry = new JRegistry;
            $registry->loadArray($array['params']);
            $array['params'] = (string) $registry;
        }

        if (isset($array['metadata']) && is_array($array['metadata']))
        {
            $registry = new JRegistry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string) $registry;
        }

        if (!JFactory::getUser()->authorise('core.admin', 'com_onecard.export_voucher.' . $array['id']))
        {
            $actions         = JAccess::getActionsFromFile(
                JPATH_ADMINISTRATOR . '/components/com_onecard/access.xml',
                "/access/section[@name='export_voucher']/"
            );
            $default_actions = JAccess::getAssetRules('com_onecard.export_voucher.' . $array['id'])->getData();
            $array_jaccess   = array();

            foreach ($actions as $action)
            {
                $array_jaccess[$action->name] = $default_actions[$action->name];
            }

            $array['rules'] = $this->JAccessRulestoArray($array_jaccess);
        }

        // Bind the rules for ACL where supported.
        if (isset($array['rules']) && is_array($array['rules']))
        {
            $this->setRules($array['rules']);
        }

        return parent::bind($array, $ignore);
    }

    /**
     * This function convert an array of JAccessRule objects into an rules array.
     *
     * @param   array  $jaccessrules  An array of JAccessRule objects.
     *
     * @return  array
     */
    private function JAccessRulestoArray($jaccessrules)
    {
        $rules = array();

        foreach ($jaccessrules as $action => $jaccess)
        {
            $actions = array();

            foreach ($jaccess->getData() as $group => $allow)
            {
                $actions[$group] = ((bool) $allow);
            }

            $rules[$action] = $actions;
        }

        return $rules;
    }

    /**
     * Overloaded check function
     *
     * @return bool
     */
    public function check()
    {
        // If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0)
        {
            $this->ordering = self::getNextOrder();
        }
		  
        // Support for subform field voucher_exported
		if (is_array($this->list_templates) && !$this->is_exported_code)
		{
			$not_enough = 0;
			$voucher_not_enough = "";
			$this->list_templates = json_encode($this->list_templates);
			$vouchers = json_decode($this->list_templates);
			$code = array();
			foreach ($vouchers as $voucher) {
				
				$exported = OnecardHelper::export_codes_by_voucher($voucher->voucher, $voucher->expired, $voucher->number);
				$code = array_merge($code,$exported);
				if (count($exported) < $voucher->number) {
					$not_enough = 1;
					$voucher_not_enough .=  $voucher->voucher." ";
				}
				
			}
			
			if ($not_enough == 1) {
				throw new Exception('Your <b>Voucher</b> item :"<b> ' . $voucher_not_enough . '</b>" is not enough');
			}else {
				$code = implode(",",$code);
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('status') . ' = 2',
					$db->quoteName('exported_id') . ' = '.$this->id
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('id') . ' IN ('.$code.')'
					
				);

				$query->update($db->quoteName('#__onecard_code'))->set($fields)->where($conditions);

				$db->setQuery($query);

				$result = $db->execute();
			}
			$this->is_exported_code = 1;
		}
		
      

        return parent::check();
    }

    /**
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    $pks     An optional array of primary key values to update.  If not
     *                            set the instance property value is used.
     * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer  $userId  The user id of the user performing the operation.
     *
     * @return   boolean  True on success.
     *
     * @since    1.0.4
     *
     * @throws Exception
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->_tbl_key;

        // Sanitize input.
        ArrayHelper::toInteger($pks);
        $userId = (int) $userId;
        $state  = (int) $state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks))
        {
            if ($this->$k)
            {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else
            {
                throw new Exception(500, JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
            }
        }

        // Build the WHERE clause for the primary keys.
        $where = $k . '=' . implode(' OR ' . $k . '=', $pks);

        // Determine if there is checkin support for the table.
        if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
        {
            $checkin = '';
        }
        else
        {
            $checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
        }

        // Update the publishing state for rows with the given primary keys.
        $this->_db->setQuery(
            'UPDATE `' . $this->_tbl . '`' .
            ' SET `state` = ' . (int) $state .
            ' WHERE (' . $where . ')' .
            $checkin
        );
        $this->_db->execute();

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
        {
            // Checkin each row.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the JTable instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks))
        {
            $this->state = $state;
        }

        return true;
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     *
     * @return string The asset name
     *
     * @see JTable::_getAssetName
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;

        return 'com_onecard.export_voucher.' . (int) $this->$k;
    }

    /**
     * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @param   JTable   $table  Table name
     * @param   integer  $id     Id
     *
     * @see JTable::_getAssetParentId
     *
     * @return mixed The id on success, false on failure.
     */
    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');

        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();

        // The item has the component as asset-parent
        $assetParent->loadByName('com_onecard');

        // Return the found asset-parent-id
        if ($assetParent->id)
        {
            $assetParentId = $assetParent->id;
        }

        return $assetParentId;
    }

    /**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return bool
     */
    public function delete($pk = null)
    {
        $this->load($pk);
        $result = parent::delete($pk);
        
        return $result;
    }
}

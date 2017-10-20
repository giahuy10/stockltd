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

/**
 * Onecard helper.
 *
 * @since  1.6
 */
class OnecardHelpersOnecard
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  string
	 *
	 * @return void
	 */
	public static function addSubmenu($vName = '')
	{
	    
             JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_BRANDS'),
            'index.php?option=com_onecard&view=brands',
            $vName == 'brands'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_CODES'),
            'index.php?option=com_onecard&view=codes',
            $vName == 'codes'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_CONTRACT'),
            'index.php?option=com_onecard&view=contracts',
            $vName == 'contracts'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_EVENTS'),
            'index.php?option=com_onecard&view=events',
            $vName == 'events'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_EXPORT VOUCHERS'),
            'index.php?option=com_onecard&view=export_vouchers',
            $vName == 'export_vouchers'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_NCC'),
            'index.php?option=com_onecard&view=nccs',
            $vName == 'nccs'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_ORDERS'),
            'index.php?option=com_onecard&view=orders',
            $vName == 'orders'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_ORDER VOUCHERS'),
            'index.php?option=com_onecard&view=order_vouchers',
            $vName == 'order_vouchers'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_PARTNERS'),
            'index.php?option=com_onecard&view=partners',
            $vName == 'partners'
        );

     JHtmlSidebar::addEntry(
            JText::_('COM_ONECARD_TITLE_VOUCHERS'),
            'index.php?option=com_onecard&view=vouchers',
            $vName == 'vouchers'
        );



	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return    JObject
	 *
	 * @since    1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_onecard';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}


class OnecardHelper extends OnecardHelpersOnecard
	
{
	public static function check_voucher_export($export_id, $voucher_id){
		$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select($db->quoteName('id'));
	$query->from($db->quoteName('#__onecard_export_voucher_detail'));
	$query->where($db->quoteName('exported_id') . ' = '. $export_id);
	$query->where($db->quoteName('voucher') . ' = '. $voucher_id);
	

	// Reset the query using our newly populated query object.
	$db->setQuery($query);

	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadResult();
	return($results);
	}
	public static function get_type_name($id) {
		if ($id == 1)
			return "E-Voucher";
		elseif ($id == 2)
			return "C-Voucher";	
		elseif ($id == 3)
			return "Product";	
		elseif ($id == 4)
			return "Gift/Discount";	
		else	
			return "E-Voucher";	
	}
	public static function get_status_name($id) {
		if ($id == 1)
			return "Trong kho";
		elseif ($id == 2)
			return "Đã xuất";	
		elseif ($id == 3)
			return "Đã sử dụng";	
		elseif ($id == 4)
			return "Gift/Discount";	
		else	
			return "Trong kho";	
	}
	public static function gen_select ($table, $current_id=NULL, $related_field= NULL, $related_table = NULL, $fkey = NULL) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query->select(array('a.id','a.title'));
			$query->from($db->quoteName('#__'.$table, 'a'));
			if ($related_table && $fkey && $related_field) {
			$query->join('INNER', $db->quoteName('#__'.$related_table, 'b') . ' ON (' . $db->quoteName('a.'.$related_field) . ' = ' . $db->quoteName('b.id') . ')');
			$query->where($db->quoteName('b.id') . ' ='.$fkey);
		}
			$query->order($db->quoteName('a.id') . ' DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();
		echo '<select name="'.$table.'" onchange="this.form.submit()">
			<option value="">-- Lọc --</option>
		';
		
		foreach ($results as $option) {
			$active = "";
			if ($current_id == $option->id)
				$active = "selected";
			echo '<option value="'.$option->id.'" '.$active.'>'.$option->title.'</option>';
		}
		echo '</select>';	
	
		
	}
	public static function doImport($fileExcel, $voucher_id, $type){
  		require_once (JPATH_COMPONENT.'/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		
		$rows = $xlsx->rows() ;
		$user       = JFactory::getUser();
  		
		$count_insert=0;
		$duplicated="";
		$code_created = "";
		$code_imported = 0;
		foreach ($rows as $row) {
				if ($count_insert>0) {
						
						$product[$count_insert] = new stdClass();						
				
						$product[$count_insert]->state = 1 ;	
												
						$product[$count_insert]->code = $row[0];
						$check_code = OnecardHelper::check_code($row[0]);
						$product[$count_insert]->barcode = $row[1];
						$product[$count_insert]->serial = $row[2];
						$product[$count_insert]->created_by = $user->id;
						$product[$count_insert]->voucher = $voucher_id;
						$product[$count_insert]->status = 1;
						$product[$count_insert]->type = $type;
					//$product[$count_insert]->value = $row[2];
					if (!$check_code) {
						OnecardHelper::import_product($product[$count_insert],"onecard_code");
						
						$code_created.='"'.$row[0].'" ';
						$code_imported++;
					}else {
						$duplicated.= '"'.$row[0].'" ';
					}	
						//echo strtotime($row[2])."<br/>";
				}
			$count_insert++;		
						
		}
		
		$message = $code_imported.' code is created';
		if ($duplicated)
		$message.= " -|- Code trùng lặp: ".$duplicated;
		
		echo $message;

		
			
		


		
}
	public static function import_product($item,$table) {
		$result = JFactory::getDbo()->insertObject('#__'.$table, $item);
	}
	public static function get_max_id($table_name){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__'.$table_name));
	
		$query->order('id DESC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,1);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		if ($results) {
			$results++;
			return ($results);
		}
			
		else {
			return 1;
		}
			
	} 
	public static function export_codes ($merchant, $value, $expired, $number){
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select(array('a.code','a.barcode','a.id'))
			->from($db->quoteName('#__onecard_code', 'a'))
			->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')')
			->where($db->quoteName('b.brand') . ' = '.$merchant)
			->where($db->quoteName('b.value') . ' = '.$value)
			->where($db->quoteName('b.expired') . ' >= '.$db->quote($expired))
			->where($db->quoteName('a.status') . ' = 1')
			->order($db->quoteName('b.expired') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadObjectList();	
		return ($exported);
	}
	public static function export_codes_by_voucher ($voucher, $expired, $number){
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select($db->quoteName('a.id'))
			->from($db->quoteName('#__onecard_code', 'a'))
			->join('INNER', $db->quoteName('#__onecard_voucher', 'b') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('b.id') . ')')
			
			->where($db->quoteName('a.voucher') . ' = '.$voucher)
			->where($db->quoteName('b.expired') . ' >= '.$db->quote($expired))
			->where($db->quoteName('a.status') . ' = 1')
			->order($db->quoteName('b.expired') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query,0,$number);
		//echo $query->__toString();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$exported = $db->loadColumn();	
		return ($exported);
	}
	public static function get_voucher_id ($brand_id, $value) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__onecard_voucher'));
		
		
		$query->where($db->quoteName('brand') . ' = '. $brand_id);
		$query->where($db->quoteName('value') . ' = '. $value);
		$query->order($db->quoteName('expired') . ' ASC');
		$db->setQuery($query,0,1);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		return ($results);
	}
	public static function get_merchant_import_report($id, $type=NULL) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('COUNT(*) as total, SUM(value) as value, SUM(price) as price');
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('merchent') . ' = '. $id);
		if ($type)
			$query->where($db->quoteName('exported') . ' = '. $type);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}
	public static function get_merchant_export_report($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('COUNT(*) as total, SUM(value) as value, SUM(price) as price');
		$query->from($db->quoteName('#__inventory_voucher_exported'));
		$query->where($db->quoteName('merchent') . ' = '. $id);
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}
	public static function get_merchant_active_report($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select('COUNT(*) as total, SUM(value) as value, SUM(price) as price');
		$query->from($db->quoteName('#__inventory_voucher_exported'));
		$query->where($db->quoteName('merchent') . ' = '. $id);
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObject();
		return ($results);
	}
	public static function get_number_code_of_voucher ($voucher_id, $type=NULL) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
	

		$query->where($db->quoteName('created_id') . ' = '. $voucher_id);
		if ($type)
			$query->where($db->quoteName('exported') . ' = '. $type);
		//echo $query->__toString();

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_vouchers ($merchant=NULL,$expired=NULL,$quantity=NULL) {
		
		// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName(array('value', 'merchent', 'event', 'type','expired','price','id')));
			$query->from($db->quoteName('#__inventory_code_created'));
			if ($merchant) {
				$query->where($db->quoteName('merchent') . ' = '. $merchant);
			}
			if ($expired){
				$expired_date = date('Y-m-d', strtotime("+".$expired." days"));
				$query->where($db->quoteName('expired') . ' <= '.$db->quote($expired_date));
			}
			
			//$query->group($db->quoteName(array('value', 'merchent','expired')));	
			$query->order('expired ASC');

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			return($results);
	} 
	public static function get_merchant_name ($id) {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('title'));
		$query->from($db->quoteName('#__onecard_brand'));
		$query->where($db->quoteName('id') . ' = '. $id);
		
		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadResult();
		return ($results);
	}
	public static function get_merchants ($extension){
		$db = JFactory::getDbo();

	// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id', 'title')));
		$query->from($db->quoteName('#__categories'));
		$query->where($db->quoteName('extension') . ' = '. $db->quote($extension));
		$query->where($db->quoteName('published') . ' = 1');


		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$merchants = $db->loadObjectList();
		return ($merchants);
	}
	public static function get_code_exported($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher_exported'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('real_expired') . ' = '. $db->quote($expired));
		//echo $query->__toString();

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_code_used($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('exported') . ' = 2');
		$query->where($db->quoteName('expired') . ' = '. $db->quote($expired));
		//echo $query->__toString();

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_code_report($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('expired') . ' = '. $db->quote($expired));
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($num_rows);
	}
	public static function get_events () {
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName(array('id','title')));
		$query->from($db->quoteName('#__content'));
		

		$query->where($db->quoteName('state') . ' = 1');
	
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$codes = $db->loadObjectList();
	
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($codes);
	}
	public static function get_code_to_renew($merchant_id, $value, $expired){
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__inventory_voucher'));
		$query->where($db->quoteName('value') . ' = '. $value);

		$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
		$query->where($db->quoteName('expired') . ' = '. $db->quote($expired));
		

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$codes = $db->loadObjectList();
	
		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		return ($codes);
	}
	public static function check_export_code ($merchant_id, $value, $number) {
		// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__inventory_voucher'));
			$query->where($db->quoteName('merchent') . ' = '. $merchant_id);
			$query->where($db->quoteName('value') . ' = '. $value);
			

			// Reset the query using our newly populated query object.
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadResult();
			return ($results);
	} 
	public static function check_code ($code) {
	

			// Get a db connection.
			$db = JFactory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);

			// Select all records from the user profile table where key begins with "custom.".
			// Order it by the ordering field.
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__onecard_code'));
			$query->where($db->quoteName('code') . ' = '. $db->quote($code));
			

			// Reset the query using our newly populated query object.
			$db->setQuery($query);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadResult();
			return ($results);
	} 
}

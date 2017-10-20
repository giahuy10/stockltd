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

jimport('joomla.application.component.controllerform');

/**
 * Table controller class.
 *
 * @since  1.6
 */
class OnecardControllerexport_voucher extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'export_vouchers';
		parent::__construct();
	}
	protected function postSaveHook(JModelLegacy $model, $validData=array()){
		$user = JFactory::getUser();
		
		if (!empty($validData['list_templates']))
		{
			$tupel = new stdClass;
			$tupel->created_by = $user->id;
			$tupel->exported_id = (int) $validData['id'];
			foreach ($validData['list_templates'] as $tmp)
			{			
				
				$tupel->voucher= $tmp['voucher'];
				$tupel->number= $tmp['number'];
				//$tupel->price= $tmp['price'];
				$tupel->expired= $tmp['expired'];
				$tupel_id = OnecardHelper::check_voucher_export($tupel->exported_id,$tupel->voucher);	
				if ($tupel_id) {
					$tupel->id = $tupel_id;
					$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher_detail', $tupel, 'id');
				}else {
					$result = JFactory::getDbo()->insertObject('#__onecard_export_voucher_detail', $tupel);
				}
				
				
					
			}
		}
	}
}

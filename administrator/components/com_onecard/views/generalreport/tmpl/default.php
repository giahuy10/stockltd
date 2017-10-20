<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Uber
 * @author     Eddy Nguyen <contact@eddynguye.com>
 * @copyright  2017 Eddy Nguyen
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;


$user       = JFactory::getUser();
$option = JRequest::getVar('option');
$view = JRequest::getVar('view');
$date_from = JRequest::getVar('date_from');
$date_to = JRequest::getVar('date_to');
$onecard_voucher = JRequest::getVar('onecard_voucher');
$onecard_brand = JRequest::getVar('onecard_brand');
function get_number_of_voucher ($voucher_id, $status=NULL) {
	
	// Get a db connection.
	$db = JFactory::getDbo();

	// Create a new query object.
	$query = $db->getQuery(true);

	// Select all records from the user profile table where key begins with "custom.".
	// Order it by the ordering field.
	$query->select('id');
	$query->from($db->quoteName('#__onecard_code'));
	if ($status) 
		$query->where($db->quoteName('status') . ' = '. $status);
	$query->where($db->quoteName('voucher') . ' = '. $voucher_id);
	//$query->order('ordering ASC');

	// Reset the query using our newly populated query object.
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	// Load the results as a list of stdClass objects (see later for more options on retrieving data).
	$results = $db->loadResult();
	return ($num_rows);
}
// Get a db connection.
$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);

// Select all articles for users who have a username which starts with 'a'.
// Order it by the created date.
// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
$query
    ->select(array('a.id as voucher_id','a.title as voucher_name','d.title as brand',  'a.expired'  ))
    ->from($db->quoteName('#__onecard_voucher', 'a'))
  
	
	->join('INNER', $db->quoteName('#__onecard_brand', 'd') . ' ON (' . $db->quoteName('a.brand') . ' = ' . $db->quoteName('d.id') . ')');
	if ($onecard_voucher)
		$query->where($db->quoteName('a.id') . ' = '.$onecard_voucher);
	if ($onecard_brand)
		$query->where($db->quoteName('d.id') . ' = '.$onecard_brand);

    $query->order($db->quoteName('a.expired') . ' ASC');

// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script>
  $( function() {
   $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
  } );
  </script>
<style>
	input, select {
		width: auto !important;
		margin-bottom: 0 !important;
	}
</style>
<h2>Báo cáo tổng hợp</h2>
<form method="post" action="index.php?option=com_onecard&view=expiredreport">
Thời gian từ <input  type="text" class="datepicker" name="date_from" onchange="this.form.submit()" value="<?php echo $date_from?>" />
 đến <input  type="text" class="datepicker" name="date_to" onchange="this.form.submit()" value="<?php echo $date_to?>" /> <a href="#" class="btn btn-success" onclick="export()" id="export"><span class="icon-download" aria-hidden="true"></span>Download Excel file</a>
 <br/> <br/>
<table class="table table-bordered" id="resultsTableExportClient">
	<tr>
		<th>ID</th>
		<th>Sản phẩm</th>
		<th>Nhãn hiệu</th>
		<th>Đã nhập</th>
		<th>Đã xuất</th>
		<th>Đã sử dụng</th>
		<th>Tồn kho</th>
		
		<th>Hết hạn</th>
	
		
	</tr>
	<tr class="noExl">
		
			<td></td>
			<td><?php OnecardHelper::gen_select("onecard_voucher",$onecard_voucher)?></td>
			<td><?php OnecardHelper::gen_select("onecard_brand",$onecard_brand)?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			
		
			<td></td>
			
		</tr>
		<input type="hidden" name="option" value="<?php echo $option?>"/>
		<input type="hidden" name="view" value="<?php echo $view?>"/>
	<?php foreach ($results as $result) {?>
		<tr>
			<td><?php echo $result->voucher_id?></td>
			<td><?php echo $result->voucher_name?></td>
			<td><?php echo $result->brand?></td>
			<td><?php echo get_number_of_voucher($result->voucher_id)?></td>
			<td><?php echo get_number_of_voucher($result->voucher_id,2)?></td>
			<td><?php echo get_number_of_voucher($result->voucher_id,3)?></td>
			<td><?php echo get_number_of_voucher($result->voucher_id,1)?></td>
			<td><?php echo date("d-m-Y",strtotime($result->expired)); ?></td>
			
			
		</tr>
	<?php }?>
</table>
</form>
<script>
	
			jQuery(document).ready(function() {
				
				$("#export").click(function(){
			 
					$("#resultsTableExportClient").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Expired_Report_<?php echo date("d-m-Y")?>"
					});
				});	
				
			});
			
	
		</script>
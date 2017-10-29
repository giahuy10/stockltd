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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_onecard/assets/css/onecard.css');
$user       = JFactory::getUser();
$option = JRequest::getVar('option');
$view = JRequest::getVar('view');

$daterange = JRequest::getVar('daterange');
echo $daterange;
$date_from = JRequest::getVar('date_from');
$date_to = JRequest::getVar('date_to');
$onecard_voucher = JRequest::getVar('onecard_voucher');
$onecard_voucher = implode(",",$onecard_voucher);
$onecard_brand = JRequest::getVar('onecard_brand');
$onecard_brand = implode(",",$onecard_brand);
$type = JRequest::getVar('type');
$type = implode(",",$type);
$unit = JRequest::getVar('unit');
$unit = implode(",",$unit);
$onecard_partner = JRequest::getVar('onecard_partner');
$onecard_partner = implode(",",$onecard_partner);
$onecard_event = JRequest::getVar('onecard_event');
$onecard_event = implode(",",$onecard_event);
//echo $date_from."-".$date_to;
// Get a db connection.
$db = JFactory::getDbo();

// Create a new query object.
$query = $db->getQuery(true);

// Select all articles for users who have a username which starts with 'a'.
// Order it by the created date.
// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
$query
    ->select(array('a.voucher as voucher_id','c.title as voucher_name','d.title as brand', 'a.number as quantity', 'a.price as price', 'b.event', 'c.type', 'a.expired', 'f.title as partner', 'e.title as event','c.unit as unit' ,'b.created as exported_date' ))
    ->from($db->quoteName('#__onecard_export_voucher_detail', 'a'))
    ->join('INNER', $db->quoteName('#__onecard_export_voucher', 'b') . ' ON (' . $db->quoteName('a.exported_id') . ' = ' . $db->quoteName('b.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_voucher', 'c') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('c.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_brand', 'd') . ' ON (' . $db->quoteName('c.brand') . ' = ' . $db->quoteName('d.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_event', 'e') . ' ON (' . $db->quoteName('b.event') . ' = ' . $db->quoteName('e.id') . ')')
	->join('INNER', $db->quoteName('#__onecard_partner', 'f') . ' ON (' . $db->quoteName('e.partner') . ' = ' . $db->quoteName('f.id') . ')');
	if ($onecard_voucher)
		$query->where($db->quoteName('c.id') . ' IN ( '.$onecard_voucher.')');
	if ($onecard_brand)
		$query->where($db->quoteName('d.id') . ' IN ( '.$onecard_brand.')');
	if ($type)
		$query->where($db->quoteName('c.type') . ' IN ( '.$type.')');
	if ($unit)
		$query->where($db->quoteName('c.unit') . ' IN ( '.$unit.')');
	if ($onecard_partner)
		$query->where($db->quoteName('f.id') . ' IN ( '.$onecard_partner.')');
	if ($onecard_event)
		$query->where($db->quoteName('e.id') . ' IN ( '.$onecard_event.')');
    if ($date_from)
		$query->where('DATE('.$db->quoteName('b.created') . ') >= '.$db->quote($date_from));
	 if ($date_to)
		$query->where('DATE('.$db->quoteName('b.created') . ') <= '.$db->quote($date_to));
	
	$query->order($db->quoteName('a.created') . ' DESC');
	//echo $query->__toString();	
// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
 
<!-- Include Date Range Picker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<h2>Báo cáo xuất</h2>
<form method="post" action="index.php?option=com_onecard&view=exportreport">
<div class="row-fluid">
	<div class="span3">
	
 <input type="text" name="daterange" value="01/01/2017 - <?php echo date("m/d/Y")?>" />

<script type="text/javascript">
$(function() {
    $('input[name="daterange"]').daterangepicker();
});
</script>
	</div>
	<div class="span3"><?php OnecardHelper::gen_select("Voucher","onecard_voucher",$onecard_voucher)?></div>
	<div class="span3"> <?php OnecardHelper::gen_select("Nhan hieu","onecard_brand",$onecard_brand)?></div>
	<div class="span3"><?php OnecardHelper::gen_select("Doi tac","onecard_partner",$onecard_partner)?></div>
	
</div>
<div class="row-fluid">
	<div class="span3"><?php OnecardHelper::gen_select("Su kien","onecard_event",$onecard_event)?></div>
	<div class="span3"> <select name="type[]" multiple >
					
					<option value="1" <?php if ($type == 1) echo "selected"?>>E-Code</option>
					<option value="2" <?php if ($type == 2) echo "selected"?>>Voucher</option>
					<option value="3" <?php if ($type == 3) echo "selected"?>>Product</option>
					<option value="4" <?php if ($type == 4) echo "selected"?>>Coupon</option>
				</select></div>
	<div class="span3"><select name="unit[]" multiple>
					
					<option value="1" <?php if ($unit == 1) echo "selected"?>>NCC</option>
					<option value="2" <?php if ($unit == 2) echo "selected"?>>OneCard</option>
					
				</select></div>
	<div class="span3"><button class="btn btn-info">search</button>
	<a href="#" class="btn btn-success" onclick="export()" id="export">
	<span class="icon-download" aria-hidden="true"></span>Download</a>
	</div>
	
</div>

 
 

				
				
				
				
<table class="table table-bordered" id="resultsTableExportClient">
	
		
	
	<tr>
		
		<th>ID</th>
		<th>Sản phẩm</th>
		<th>Nhãn hiệu</th>
		<th>Số lượng</th>
		<th>Giá xuất</th>
		<th>Loại</th>
		<th>Tổng</th>
		<th>Phân phối</th>
		
		<th>Đối tác</th>
		<th>Sự kiện</th>
		<th>Ngày xuất</th>
		
	</tr>
	

		<input type="hidden" name="option" value="<?php echo $option?>"/>
		<input type="hidden" name="view" value="<?php echo $view?>"/>
		
	<?php foreach ($results as $result) {?>
		<tr>
			
			<td><?php echo $result->voucher_id?></td>
			<td><?php echo $result->voucher_name?></td>
			<td><?php echo $result->brand?></td>
			<td><?php echo $result->quantity?></td>
			<td><?php echo number_format($result->price)?></td>
			<th><?php echo OnecardHelper::get_type_name($result->type)?></th>
			<td><?php echo number_format($result->quantity*$result->price)?></td>
			<td><?php if ($result->unit == 2) echo "OneCard"; else echo "NCC"?></td>
			
			<td><?php echo $result->partner?></td>
			<td><?php echo $result->event?></td>
			<td><?php echo date("d-m-Y",strtotime($result->exported_date)); ?></td>
			
		</tr>
	<?php }?>
</table>
<script>
	
			jQuery(document).ready(function() {
				
				$("#export").click(function(){
			 
					$("#resultsTableExportClient").table2excel({
						exclude: ".noExl",
						name: "Results",
						filename: "Code_Export_<?php echo date("d-m-Y")?>"
					});
				});	
				
			});
			
	
		</script>
</form>
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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_onecard/css/form.css');
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="<?php echo JUri::root()?>administrator/components/com_inventory/assets/js/jquery.table2excel.js"></script>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {
	
    });

    Joomla.submitbutton = function (task) {
        if (task == 'export_voucher.cancel') {
            Joomla.submitform(task, document.getElementById('export_voucher-form'));
        }
        else {
            
            if (task != 'export_voucher.cancel' && document.formvalidator.isValid(document.id('export_voucher-form'))) {
                
                Joomla.submitform(task, document.getElementById('export_voucher-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_onecard&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="export_voucher-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_ONECARD_TITLE_EXPORT_VOUCHER', true)); ?>
        
        <div class="row-fluid">
            <div class="span10 form-horizontal">
			<?php if ($this->item->is_exported_code) {?>
				<?php 
					$db = JFactory::getDbo();

					// Create a new query object.
					$query = $db->getQuery(true);

					// Select all articles for users who have a username which starts with 'a'.
					// Order it by the created date.
					// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
					$query
						->select(array('a.id','a.code','a.barcode','c.title','f.expired','c.value' ))
						->from($db->quoteName('#__onecard_code', 'a'))
						->join('INNER', $db->quoteName('#__onecard_export_voucher', 'b') . ' ON (' . $db->quoteName('a.exported_id') . ' = ' . $db->quoteName('b.id') . ')')
						->join('INNER', $db->quoteName('#__onecard_voucher', 'c') . ' ON (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('c.id') . ')')
						
						->join('INNER', $db->quoteName('#__onecard_export_voucher_detail', 'f') . ' ON (' . $db->quoteName('a.exported_id') . ' = ' . $db->quoteName('f.exported_id') . ') and (' . $db->quoteName('a.voucher') . ' = ' . $db->quoteName('f.voucher') . ')');
					
							$query->where($db->quoteName('a.exported_id') . ' = '.$this->item->id);
						
						$query->group($db->quoteName('a.code'));
						$query->order($db->quoteName('a.voucher') . ' DESC');
						//echo $query->__toString();	
					// Reset the query using our newly populated query object.
					$db->setQuery($query);

					// Load the results as a list of stdClass objects (see later for more options on retrieving data).
					$exported_codes = $db->loadObjectList();
				?>
				
				<a href="#" id="export_code_btn" class="btn btn-info"><span class="icon-download" aria-hidden="true"></span> Download Codes</a>
				
				<table id="export_code_z" class="table table-bordered">
					<tr>
						
						<th>Tên Voucher</th>
						<th>Giá trị</th>
						<th>Code</th>
						<th>Barcode</th>		
						<th>Hạn sử dụng</th>
					</tr>
					<?php foreach ($exported_codes as $code) {?>
						<tr>
							<td><?php echo $code->title?></td>
							<td><?php echo $code->value?></td>
							<td><?php echo $code->code?></td>
							<td><?php echo $code->barcode?></td>
							<td><?php echo $code->expired?></td>
						</tr>
					<?php }?>
				</table>
			<?php }?>
                <fieldset class="adminform">
				
				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
<?php echo $this->form->renderField('ordering'); ?>

<?php echo $this->form->renderField('event'); ?>

<?php 
if ($this->item->id)
echo $this->form->renderField('list_templates'); ?>

<?php echo $this->form->renderField('created'); ?>
<?php echo $this->form->renderField('checked_out'); ?>
<?php echo $this->form->renderField('checked_out_time'); ?>
<?php echo $this->form->renderField('created_by'); ?>
<?php echo $this->form->renderField('modified_by'); ?>


                   
                </fieldset>
            </div>
        </div>
        
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
<!-- XUẤT VOUCHER CHO KHÁCH HÀNG-->	
<div class="modal hide fade" id="modal-export2">
	  <div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3>Upload Voucher từ file excel <a href="<?php echo JURI::root()?>mau-file-xuat-code-cho-khach-hang.xlsx">File mẫu</a></h3>
	  </div>
	  <div class="modal-body">
		<div class="container">
			 <form name="importForm" action="index.php?option=com_onecard&view=export_voucher&layout=edit&id=<?php echo $this->item->id?>" method="post" enctype="multipart/form-data" id="importForm">
					<p>Chọn file </p>
					<input type="file" name="fileToUpload" id="excel_file" size="40" class="inputbox" />
					<font color="red">(Max:&nbsp;<?php echo ini_get('upload_max_filesize'); ?>)</font>
					
						
					<p>Hạn sử dụng</p>
						
					<input type="date" name="expired" value="<?php echo date('Y-m-d')?>" required />
					
					<br/>
					<br/>
					<input type="submit" name="export" value="Xuất voucher" class="btn"/>	
					<input type="hidden" name="option" value="com_onecard" />
					<input type="hidden" name="layout" value="edit" />
					<input type="hidden" name="id" value="<?php echo $this->item->id?>" />
					<input type="hidden" name="voucher_id" value="<?php echo $this->item->id?>" />
					<input type="hidden" name="view" value="export_voucher" />
					
					<?php echo JHTML::_( 'form.token' ); ?>
				</form>
		</div>
	  </div>
	  <div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
		  <?php echo JText::_('JCANCEL'); ?>
		</button>
	  </div>
	</div> 
	<?php 
	$jinput = JFactory::getApplication()->input;
	$export = $jinput->get('export');
	if ($export) {
	
	$voucher_id = $jinput->get('voucher_id');
	$expired = $jinput->get('expired');
		$target_dir = JPATH_ROOT.'/images/import/';
		$target_file = $target_dir . date('m-d-Y_his').basename($_FILES["fileToUpload"]["name"]);
		$uploadOk = 1;
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<p class='text-danger'>Sorry, file already exists.</p>";
			$uploadOk = 0;
		}


		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<p class='text-danger'>Sorry, your file was not uploaded.</p>";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			   // echo "<p class='text-success'>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
			   
			   if($imageFileType == "xlsx") {

				   doExport($target_file, $voucher_id, $expired);
				   
			   }else {
				   //doImportxls($target_file);
			   }
				
						
				/*if (!unlink($target_file))
					{
				  echo ("Error deleting $target_file");
				  }
				else
				  {
				  //echo ("<p class='text-info'>Deleted $target_file</p>");
				}*/
			} else {
				echo "<p class='text-danger'>Sorry, there was an error uploading your file.</p>";
			}
		}
	}
		
	function doExport($fileExcel, $voucher_id, $expired){
  		
  		
		//$inventory_code_uploaded = JFactory::getDbo()->insertObject('#__inventory_code_uploaded', $inventory_code_uploaded);
  		require_once (JPATH_COMPONENT.'/libs/simplexlsx.class.php');
		$xlsx = new SimpleXLSX($fileExcel);		
		$check_conditional = 0;
		$rows = $xlsx->rows() ;
		
		$remove_row = 0;
		foreach ($rows as $check_row) {
			if ($remove_row > 0) {
				$check_nr = $check_row[0];	
				$check_client = $check_row[1];
				$check_merchant_id = $check_row[3];
				$check_merchant_name = OnecardHelper::get_merchant_name($check_merchant_id);
				$check_value = $check_row[4];
				$check_quantity = $check_row[5];
				$check_exported = OnecardHelper::export_codes($check_merchant_id, $check_value, $expired, $check_quantity);
				$check_number_code = count($check_exported);
				if ($check_number_code < $check_quantity) {
					$unvailable[$check_nr] = new stdClass();
					$unvailable[$check_nr]->check_nr=$check_nr;
					$unvailable[$check_nr]->check_client=$check_client;
					$unvailable[$check_nr]->check_merchant_name=$check_merchant_name;
					$unvailable[$check_nr]->check_value=$check_value;
					$unvailable[$check_nr]->check_quantity=$check_quantity;
					$unvailable[$check_nr]->check_available_code=$check_number_code;
					$check_conditional = 1;
				}
			}
			$remove_row++;	
		}
		//echo "<pre>";
		//var_dump($unvailable);
		//echo "</pre>";
		if ($check_conditional == 1) {?> 
			<h2>Code không đủ</h2>
			<table class="table table-bordered" id="warningTableExportClient">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên Khách hàng</th>
					<th>NCC</th>
					<th>Giá trị</th>
					<th>Số lượng</th>
					<th>Code khả dụng</th>
				
				
					
				</tr>
			</thead>
			<tbody>
			<?php foreach ($unvailable as $unvailable_code) {?>
				<tr>
					<td><?php echo $unvailable_code->check_nr?></td>
					<td><?php echo $unvailable_code->check_client?></td>
					<td><?php echo $unvailable_code->check_merchant_name?></td>
					<td><?php echo $unvailable_code->check_value?></td>
					<td><?php echo $unvailable_code->check_quantity?></td>
					<td><?php echo $unvailable_code->check_available_code?></td>
				</tr>
			<?php }?>
			</tbody>
			</table>
			
		<?php } else {
		$count_insert=0;
		?>
		<h2>Code đã xuất</h2>
		<table class="table table-bordered" id="resultsTableExportClient">
			<thead>
				<tr>
					<th>STT</th>
					<th>Tên Khách hàng</th>
					<th>SĐT</th>
					<th>NCC</th>
					<th>Giá trị</th>
					<th>Số lượng</th>
					<th>Code - BarCode</th>
				
					<th>Hạn sử dụng</th>
					<th>Đối tác</th>
					<th>Giá xuất</th>
				</tr>
			</thead>
			<tbody>
			
		<?php 
		$data_json = "";
		foreach ($rows as $row) {
				if ($count_insert>0) {
						$exported_code = new stdClass();
						$nr = $row[0];	
						$client = $row[1];
						$phone = $row[2];
						$merchant_id = $row[3];
						$merchant_name = OnecardHelper::get_merchant_name($merchant_id);
						$value = $row[4];
						$quantity = $row[5];
						$price = $row[6];
						//$event="";
						//$expired = date('Y-m-d', strtotime("+35 days"));
					?>
						<tr>
							<td><?php echo $nr?></td>
							<td><?php echo $client?></td>
							<td><?php echo $phone?></td>
							<td><?php echo $merchant_name?></td>
							<td><?php echo $value?></td>
							<td><?php echo $quantity?></td>
							<td>
					<?php
						
						$exported = OnecardHelper::export_codes($merchant_id, $value, $expired, $quantity);
						if ($exported) {
					
						$user = JFactory::getUser();
						$exported_code_table = new stdClass();
						$exported_code_table->voucher=OnecardHelper::get_voucher_id($merchant_id,$value);
						$exported_code_table->created_by = $user->id;
						$exported_code_table->number = $quantity;
						$exported_code_table->price = $price;
						$exported_code_table->expired = $expired;
						
						$exported_code_table->exported_id = $voucher_id;
						
						$insert_code_exported = JFactory::getDbo()->insertObject('#__onecard_export_voucher_detail', $exported_code_table);
						$json_id = $count_insert-1;
						$data_json .= '"list_templates'.$json_id.'":{"voucher":"'.$exported_code_table->voucher.'","number":"'.$quantity.'","price":"'.$price.'","expired":"'.$expired.'"}';
						$real_record = count($rows) - 1;
						if ($count_insert < $real_record){
							$data_json .=',';
						}
						$z=0;
						foreach ($exported as $code) {
							echo $code->code;
							if ($code->barcode)
								echo "-".$code->barcode;
							$z++;
							if ($z<$quantity) echo " | ";
							
							$code->status=2;
							$code->exported_id=$voucher_id;
							$update = JFactory::getDbo()->updateObject('#__onecard_code', $code, 'id');
						}?>
							</td>
							
							<td><?php echo $expired?></td>
							<td><?php echo OnecardHelper::get_merchant_name($merchant_id)?></td>
							<td><?php echo $price?></td>
						</tr>
						<?php 
					}else {
						echo "
							<script>
								alert('Không còn đủ CODE');
							</script>
						";
					}
						
				}
				$count_insert++;	
						
		}
		$object = new stdClass();

		// Must be a valid primary key value.
		$object->id = $voucher_id;
	
		$object->list_templates = '{'.$data_json.'}';
		$object->is_exported_code = 1;
		// Update their details in the users table using id as the primary key.
		$result = JFactory::getDbo()->updateObject('#__onecard_export_voucher', $object, 'id');
		?>
			</tbody>
			</table>
		
		<?php
	
		}
			//header ("Location: index.php?option=com_inventory&view=vouchers");
		


		
}	
	
	?>
	<script>
			jQuery(document).ready(function($) {
				$('#export_code_btn').on('click', function(e){
										e.preventDefault();
										ResultsToTable();
									});
									
									function ResultsToTable(){    
										$("#export_code_z").table2excel({
											exclude: ".noExl",
											name: "Results",
											filename: "Code_Exported_<?php echo $this->item->event?>"
										});
									}
				
			});
		</script>
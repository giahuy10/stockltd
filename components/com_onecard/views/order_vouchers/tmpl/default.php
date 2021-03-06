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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_onecard');
$canEdit    = $user->authorise('core.edit', 'com_onecard');
$canCheckin = $user->authorise('core.manage', 'com_onecard');
$canChange  = $user->authorise('core.edit.state', 'com_onecard');
$canDelete  = $user->authorise('core.delete', 'com_onecard');
?>

<form action="<?php echo JRoute::_('index.php?option=com_onecard&view=order_vouchers'); ?>" method="post"
      name="adminForm" id="adminForm">

	<?php   echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
	
	
	
	<table class="table table-striped" id="order_voucherList">
		<thead>
		<tr>
		
		
			
<?php if (isset($this->items[0]->state)): ?>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
					</th>
				<?php endif; ?>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_FORM_LBL_order_voucher_ID', 'a.`id`', $listDirn, $listOrder); ?>		</th>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_ORDER_VOUCHERS_CREATED_BY', 'a.`created_by`', $listDirn, $listOrder); ?>		</th>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_ORDER_VOUCHERS_MODIFIED_BY', 'a.`modified_by`', $listDirn, $listOrder); ?>		</th>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_ORDER_VOUCHERS_ORDER_ID', 'a.`order_id`', $listDirn, $listOrder); ?>		</th>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_ORDER_VOUCHERS_VOUCHER_ID', 'a.`voucher_id`', $listDirn, $listOrder); ?>		</th>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_ORDER_VOUCHERS_QUANTITY', 'a.`quantity`', $listDirn, $listOrder); ?>		</th>
<th>		<?php echo JHtml::_('grid.sort', 'COM_ONECARD_ORDER_VOUCHERS_PRICE', 'a.`price`', $listDirn, $listOrder); ?>		</th>
                	

				<?php if ($canEdit || $canDelete): ?>
					<th class="center">
				<?php echo JText::_('COM_ONECARD_LANG_TAG_ACTIONS'); ?>
				</th>
				<?php endif; ?>

		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_onecard'); ?>

				<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_onecard')): ?>
					<?php // $canEdit = JFactory::getUser()->id == $item->created_by; ?>
				<?php endif; ?>

			<tr class="row<?php echo $i % 2; ?>">
			
			
			

				<?php if (isset($this->items[0]->state)) : ?>
					<?php $class = ($canChange) ? 'active' : 'disabled'; ?>
					<td class="center">
						<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canChange) ? JRoute::_('index.php?option=com_onecard&view=order_voucher.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
						<?php if ($item->state == 1): ?>
							<i class="icon-publish"></i>
						<?php else: ?>
							<i class="icon-unpublish"></i>
						<?php endif; ?>
						</a>
					</td>
				<?php endif; ?>
<td>		<?php echo $item->id; ?>		</td>
<td>		<?php echo $item->created_by; ?>		</td>
<td>		<?php echo $item->modified_by; ?>		</td>
<td>		<?php echo $item->order_id; ?>		</td>
<td>		<?php echo $item->voucher_id; ?>		</td>
<td>		<?php echo $item->quantity; ?>		</td>
<td>		<?php echo $item->price; ?>		</td>
			

				<?php if ($canEdit || $canDelete): ?>
					<td class="center">
						<?php if ($canEdit): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_onecard&task=order_voucherform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
						<?php endif; ?>
						<?php if ($canDelete): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_onecard&task=order_voucherform.remove&id=' . $item->id, false, 2); ?>" class="btn btn-mini delete-button" type="button"><i class="icon-trash" ></i></a>
						<?php endif; ?>
					</td>
				<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($canCreate) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_onecard&task=order_voucherform.edit&id=0', false, 2); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo JText::_('COM_ONECARD_LANG_TAG_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php if($canDelete) : ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo JText::_('COM_ONECARD_LANG_TAG_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>
<?php endif; ?>

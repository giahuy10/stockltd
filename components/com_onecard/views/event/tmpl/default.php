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

$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::base() . '/media/com_onecard/css/item.css');
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_onecard');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_onecard')) {
	// $canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

	<div class="item_fields">
	
	
		<table class="table">
				<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_ID'); ?></th><td>
					<?php echo $this->item->id; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_STATE'); ?></th><td><?php echo ($this->item->state == 1) ? JText::_('JPUBLISH') : JText::_('JUNPUBLISH'); ?></td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_CREATED_BY'); ?></th><td>
					<?php echo $this->item->created_by; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_MODIFIED_BY'); ?></th><td>
					<?php echo $this->item->modified_by; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_TITLE'); ?></th><td>
					<?php echo $this->item->title; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_DESCRIPTION'); ?></th><td>
					<?php echo $this->item->description; ?>
					
				</td></tr>
<tr><th><?php echo JText::_('COM_ONECARD_COM_ONECARD_FORM_LBL_EVENT_PARTNER'); ?></th><td>
					<?php echo $this->item->partner; ?>
					
				</td></tr>

		</table>
		
		
	</div>
	<?php if($canEdit && $this->item->checked_out == 0): ?>
		<button type="button" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_onecard&task=event.edit&id='.$this->item->id); ?>';">
			<?php echo JText::_("COM_ONECARD_EDIT_ITEM_EVENT"); ?>
		</button>
	<?php endif; ?>
	<?php if(JFactory::getUser()->authorise('core.delete','com_onecard')):?>
		<button type="button" onclick="window.location.href='<?php echo JRoute::_('index.php?option=com_onecard&task=event.remove&id=' . $this->item->id, false, 2); ?>';">
				<?php echo JText::_("COM_ONECARD_DELETE_ITEM_EVENT"); ?>
		</button>
	<?php endif; ?>
	<?php
else:
	echo JText::_('COM_ONECARD_ITEM_NOT_LOADED_EVENT');
endif;

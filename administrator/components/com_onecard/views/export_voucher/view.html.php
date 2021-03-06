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

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  1.6
 */
class OnecardViewExport_voucher extends JViewLegacy
{
    protected $state;

    protected $item;

    protected $form;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @throws Exception
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);
		$toolbar = JToolBar::getInstance('toolbar');
		$layout = new JLayoutFile('joomla.toolbar.popup');
        $user  = JFactory::getUser();
        $isNew = ($this->item->id == 0);

        if (isset($this->item->checked_out))
        {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        }
        else
        {
            $checkedOut = false;
        }
		if (!$isNew) {
			$export_2 = $layout->render(array('name' => 'export2','doTask' => '', 'text' => JText::_('Xuất code cho khách (excel)'), 'class' => 'icon-download'));
			$toolbar->appendButton('Custom', $export_2);
		}
        $canDo = OnecardHelpersOnecard::getActions();

        JToolBarHelper::title(JText::_('COM_ONECARD_TITLE_EXPORT_VOUCHER'), 'export_voucher.png');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
        {
            JToolBarHelper::apply('export_voucher.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('export_voucher.save', 'JTOOLBAR_SAVE');
        }

        if (!$checkedOut && ($canDo->get('core.create')))
        {
            JToolBarHelper::custom('export_voucher.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create'))
        {
            JToolBarHelper::custom('export_voucher.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        }

        // Button for version control
        if ($this->state->params->get('save_history', 1) && $user->authorise('core.edit')) {
            JToolbarHelper::versions('com_onecard.export_voucher', $this->item->id);
        }

        if (empty($this->item->id))
        {
            JToolBarHelper::cancel('export_voucher.cancel', 'JTOOLBAR_CANCEL');
        }
        else
        {
            JToolBarHelper::cancel('export_voucher.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}

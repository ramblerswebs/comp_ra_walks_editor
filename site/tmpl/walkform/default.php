<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ra_walks_editor
 * @author     Chris Vaughan  <ruby.tuesday@ramblers-webs.org.uk>
 * @copyright  2024 ruby.tuesday@ramblers-webs.org.uk
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;
use \Ramblers\Component\Ra_walks_editor\Site\Helper\Ra_walks_editorHelper;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
        ->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_ra_walks_editor', JPATH_SITE);

$user = Factory::getApplication()->getIdentity();
$canEdit = Ra_walks_editorHelper::canUserEdit($this->item, $user);

echo '<div class="walk-edit front-end-edit">';
if (!$canEdit) {
    // echo    '<h3>';
    throw new \Exception(Text::_('COM_RA_WALKS_EDITOR_ERROR_MESSAGE_NOT_AUTHORISED'), 403);
    //  echo   '</h3>';
} else {
    if (!empty($this->item->id)) {
        echo '<h1>' . Text::sprintf('COM_RA_WALKS_EDITOR_EDIT_ITEM_TITLE', $this->item->id) . '</h1>';
    } else {
        echo '<h1>' . Text::_('COM_RA_WALKS_EDITOR_ADD_ITEM_TITLE') . '</h1>';
    }
    ?>
    <form id="form-walk" class="hidden"
          action="<?php echo Route::_('index.php?option=com_ra_walks_editor&task=walkform.save'); ?>"
          method="post" class="form-validate form-horizontal" enctype="multipart/form-data">

        <input type="hidden" name="jform[ordering]" value="<?php echo isset($this->item->ordering) ? $this->item->ordering : ''; ?>" />

        <input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

        <input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />

        <input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

        <?php
        echo $this->form->getInput('created_by');
        echo $this->form->getInput('modified_by');
        echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'DraftWalksandEvents'));
        echo HTMLHelper::_('uitab.addTab', 'myTab', 'DraftWalksandEvents', Text::_('COM_RA_WALKS_EDITOR_TAB_DRAFTWALKSANDEVENTS', true));
        echo $this->form->renderField('date');
        echo $this->form->renderField('id');
        echo $this->form->renderField('category');
        echo $this->form->renderField('content');
        echo $this->form->renderField('status');
        echo HTMLHelper::_('uitab.endTab');
        ?>
        <div class="control-group">
            <div class="controls">

                    <?php if ($this->canSave): ?>
                    <button type="submit" class="validate btn btn-primary" id="js-submitbtn">
                        <span class="fas fa-check" aria-hidden="true"></span>
                    <?php echo Text::_('JSUBMIT'); ?>
                    </button>
    <?php endif; ?>
                <a class="btn btn-danger"
                   href="<?php echo Route::_('index.php?option=com_ra_walks_editor&task=walkform.cancel'); ?>"
                   title="<?php echo Text::_('JCANCEL'); ?>">
                    <span class="fas fa-times" aria-hidden="true"></span>
    <?php echo Text::_('JCANCEL'); ?>
                </a>
            </div>
        </div>

        <input type="hidden" name="option" value="com_ra_walks_editor"/>
        <input type="hidden" name="task"
               value="walkform.save"/>
    <?php echo HTMLHelper::_('form.token'); ?>
    </form> 
    </div>

    <?php
}
$style = 'form.hidden {display:none;}';
JFactory::getDocument()->addStyleDeclaration($style);
$loader = new Ramblers\Component\Ra_walks_editor\Site\Loader();
$loader->fields['submit'] = "js-submitbtn";
$loader->fields['content'] = "jform_content";
$loader->fields['date'] = "jform_date";
$loader->fields['status'] = "jform_status";
$loader->fields['category'] = "jform_category";
$loader->fields['cancel'] = Route::_('index.php?option=com_ra_walks_editor&task=walkform.cancel');
$loader->editWalk($walkdate);

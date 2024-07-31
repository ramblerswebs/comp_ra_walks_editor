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

$user    = Factory::getApplication()->getIdentity();
$canEdit = Ra_walks_editorHelper::canUserEdit($this->item, $user);


?>

<div class="place-edit front-end-edit">
	<?php if (!$canEdit) : ?>
		<h3>
		<?php throw new \Exception(Text::_('COM_RA_WALKS_EDITOR_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
		</h3>
	<?php else : ?>
		<?php if (!empty($this->item->id)): ?>
			<h1><?php echo Text::sprintf('COM_RA_WALKS_EDITOR_EDIT_ITEM_TITLE', $this->item->id); ?></h1>
		<?php else: ?>
			<h1><?php echo Text::_('COM_RA_WALKS_EDITOR_ADD_ITEM_TITLE'); ?></h1>
		<?php endif; ?>

		<form id="form-place"
			  action="<?php echo Route::_('index.php?option=com_ra_walks_editor&task=placeform.save'); ?>"
			  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			
	<input type="hidden" name="jform[ordering]" value="<?php echo isset($this->item->ordering) ? $this->item->ordering : ''; ?>" />

	<input type="hidden" name="jform[state]" value="<?php echo isset($this->item->state) ? $this->item->state : ''; ?>" />

	<input type="hidden" name="jform[checked_out]" value="<?php echo isset($this->item->checked_out) ? $this->item->checked_out : ''; ?>" />

	<input type="hidden" name="jform[checked_out_time]" value="<?php echo isset($this->item->checked_out_time) ? $this->item->checked_out_time : ''; ?>" />

				<?php echo $this->form->getInput('created_by'); ?>
				<?php echo $this->form->getInput('modified_by'); ?>
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'place')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'place', Text::_('COM_RA_WALKS_EDITOR_TAB_PLACE', true)); ?>
	<?php echo $this->form->renderField('name'); ?>

	<?php echo $this->form->renderField('id'); ?>

	<?php echo $this->form->renderField('abbr'); ?>

	<?php echo $this->form->renderField('postcode'); ?>

	<?php echo $this->form->renderField('latitude'); ?>

	<?php echo $this->form->renderField('longitude'); ?>

	<?php echo $this->form->renderField('gridreference'); ?>

	<?php echo $this->form->renderField('what3words'); ?>

	<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<div class="control-group">
				<div class="controls">

					<?php if ($this->canSave): ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger"
					   href="<?php echo Route::_('index.php?option=com_ra_walks_editor&task=placeform.cancel'); ?>"
					   title="<?php echo Text::_('JCANCEL'); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_ra_walks_editor"/>
			<input type="hidden" name="task"
				   value="placeform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	<?php endif; ?>
</div>

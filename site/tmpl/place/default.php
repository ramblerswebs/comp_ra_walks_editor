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
use \Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

$canEdit = Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_ra_walks_editor');

if (!$canEdit && Factory::getApplication()->getIdentity()->authorise('core.edit.own', 'com_ra_walks_editor'))
{
	$canEdit = Factory::getApplication()->getIdentity()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_ABBR'); ?></th>
			<td><?php echo $this->item->abbr; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_POSTCODE'); ?></th>
			<td><?php echo $this->item->postcode; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_LATITUDE'); ?></th>
			<td><?php echo $this->item->latitude; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_LONGITUDE'); ?></th>
			<td><?php echo $this->item->longitude; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_GRIDREFERENCE'); ?></th>
			<td><?php echo $this->item->gridreference; ?></td>
		</tr>

		<tr>
			<th><?php echo Text::_('COM_RA_WALKS_EDITOR_FORM_LBL_PLACE_WHAT3WORDS'); ?></th>
			<td><?php echo $this->item->what3words; ?></td>
		</tr>

	</table>

</div>

<?php $canCheckin = Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_ra_walks_editor.' . $this->item->id) || $this->item->checked_out == Factory::getApplication()->getIdentity()->id; ?>
	<?php if($canEdit && $this->item->checked_out == 0): ?>

	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_ra_walks_editor&task=place.edit&id='.$this->item->id); ?>"><?php echo Text::_("COM_RA_WALKS_EDITOR_EDIT_ITEM"); ?></a>
	<?php elseif($canCheckin && $this->item->checked_out > 0) : ?>
	<a class="btn btn-outline-primary" href="<?php echo Route::_('index.php?option=com_ra_walks_editor&task=place.checkin&id=' . $this->item->id .'&'. Session::getFormToken() .'=1'); ?>"><?php echo Text::_("JLIB_HTML_CHECKIN"); ?></a>

<?php endif; ?>

<?php if (Factory::getApplication()->getIdentity()->authorise('core.delete','com_ra_walks_editor.place.'.$this->item->id)) : ?>

	<a class="btn btn-danger" rel="noopener noreferrer" href="#deleteModal" role="button" data-bs-toggle="modal">
		<?php echo Text::_("COM_RA_WALKS_EDITOR_DELETE_ITEM"); ?>
	</a>

	<?php echo HTMLHelper::_(
                                    'bootstrap.renderModal',
                                    'deleteModal',
                                    array(
                                        'title'  => Text::_('COM_RA_WALKS_EDITOR_DELETE_ITEM'),
                                        'height' => '50%',
                                        'width'  => '20%',
                                        
                                        'modalWidth'  => '50',
                                        'bodyHeight'  => '100',
                                        'footer' => '<button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button><a href="' . Route::_('index.php?option=com_ra_walks_editor&task=place.remove&id=' . $this->item->id, false, 2) .'" class="btn btn-danger">' . Text::_('COM_RA_WALKS_EDITOR_DELETE_ITEM') .'</a>'
                                    ),
                                    Text::sprintf('COM_RA_WALKS_EDITOR_DELETE_CONFIRM', $this->item->id)
                                ); ?>

<?php endif; ?>
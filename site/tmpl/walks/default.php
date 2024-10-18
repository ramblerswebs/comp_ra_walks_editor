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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\User\UserFactoryInterface;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getApplication()->getIdentity();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canCreate = $user->authorise('core.create', 'com_ra_walks_editor') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'walkform.xml');
$canEdit = $user->authorise('core.edit', 'com_ra_walks_editor') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'walkform.xml');
$canCheckin = $user->authorise('core.manage', 'com_ra_walks_editor');
$canChange = $user->authorise('core.edit.state', 'com_ra_walks_editor');
$canDelete = $user->authorise('core.delete', 'com_ra_walks_editor');

// Import CSS
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_ra_walks_editor.list');

if ($canDelete) {
    $wa->addInlineScript("
			jQuery(document).ready(function () {
				jQuery('.delete-button').click(deleteItem);
			});

			function deleteItem() {

				if (!confirm(\"" . Text::_('COM_RA_WALKS_EDITOR_DELETE_MESSAGE') . "\")) {
					return false;
				}
			}
		", [], [], ["jquery"]);
}

// Additional code to use javascript

$data = new Class {
    
};
$data->$userId = $userId;
$data->items = $this->items;
$data->newUrl = null;

if ($canCreate) {
    $data->newUrl = JRoute::_('index.php?option=com_ra_walks_editor&task=walkform.edit&id=0', false, 2);
}
foreach ($this->items as $item) {
    $item->deleteUrl = null;
    $item->editUrl = null;
    $item->duplicateUrl = null;
    $item->viewUrl = JRoute::_('index.php?option=com_ra_walks_editor&view=walk&id=' . (int) $item->id);
    if ($canDelete) {
        $item->deleteUrl = JRoute::_('index.php?option=com_ra_walks_editor&task=walkform.remove&id=' . $item->id, false, 2);
    }
    if ($canEdit) {
        $item->editUrl = JRoute::_('index.php?option=com_ra_walks_editor&task=walk.edit&id=' . $item->id, false, 2);
    }
    if ($canCreate) {
        $item->duplicateUrl = JRoute::_('index.php?option=com_ra_walks_editor&task=walkform.edit&copy=1&id=' . $item->id, false, 2);
    }
}

$loader = new Ramblers\Component\Ra_walks_editor\Site\Loader();
$loader->fields['content'] = 'js-contents';
$loader->viewWalks($data);
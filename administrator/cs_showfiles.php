<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Cs_showfiles
 * @author     Ted Lowe <lists@creativespirits.org>
 * @copyright  reative Spirits (c) 2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_cs_showfiles'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Cs_showfiles', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Cs_showfilesHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'cs_showfiles.php');

$controller = JControllerLegacy::getInstance('Cs_showfiles');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

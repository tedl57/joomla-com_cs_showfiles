<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Cs_showfiles
 * @author     Ted Lowe <lists@creativespirits.org>
 * @copyright  reative Spirits (c) 2018
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Showfiles list controller class.
 *
 * @since  1.6
 */
class Cs_showfilesControllerShowfiles extends Cs_showfilesController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Showfiles', $prefix = 'Cs_showfilesModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}

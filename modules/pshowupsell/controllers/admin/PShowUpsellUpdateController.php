<?php

/**
 * File from https://prestashow.pl
 *
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 *  @authors     PrestaShow.pl <kontakt@prestashow.pl>
 *  @copyright   2018 PrestaShow.pl
 *  @license     https://prestashow.pl/license
 */

require_once dirname(__FILE__) . "/../../config.php";

use Prestashow\PrestaCore\Controller\UpdateController;

class PShowUpsellUpdateController extends UpdateController {

    public $select_menu_tab = 'subtab-PShowUpsellMain';

}

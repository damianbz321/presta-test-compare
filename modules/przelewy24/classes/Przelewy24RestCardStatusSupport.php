<?php
/**
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24RestCardStatusSupport
 */
class Przelewy24RestCardStatusSupport extends Przelewy24RestStatusSupport implements Przelewy24StatusSupportInterface
{
    /**
     * Possible card to save.
     *
     * @return bool
     */
    public function possibleCardToSave()
    {
        return true;
    }
}

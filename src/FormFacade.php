<?php
namespace olafnorge\Html;

use Collective\Html\FormFacade as BaseFormFacade;

/**
 * Class FormFacade
 *
 * @package olafnorge\Html
 * @see \olafnorge\Html\FormBuilder
 */
class FormFacade extends BaseFormFacade {


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return parent::getFacadeAccessor();
    }
}

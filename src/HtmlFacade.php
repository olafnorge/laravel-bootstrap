<?php
namespace olafnorge\Html;

use Collective\Html\HtmlFacade as BaseHtmlFacade;

/**
 * Class HtmlFacade
 *
 * @package olafnorge\Html
 * @see \olafnorge\Html\HtmlBuilder
 */
class HtmlFacade extends BaseHtmlFacade {


    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return parent::getFacadeAccessor();
    }
}

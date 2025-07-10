<?php

namespace App\Support;

use Illuminate\Contracts\Support\Htmlable;

/**
 * A simple class to wrap a string of HTML and mark it as safe.
 * This prevents Laravel's blade templates from escaping the HTML.
 */
class HtmlString implements Htmlable
{
    protected $html;

    public function __construct($html)
    {
        $this->html = $html;
    }

    public function toHtml()
    {
        return $this->html;
    }
}

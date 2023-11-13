<?php

namespace Netflex\Toolbox\Views\Components\Recaptcha;

use Illuminate\View\Component;

class Checkbox extends Component
{
    public ?string $push;
    public bool $scriptOnly;

    public function __construct(?string $push = 'head', bool $scriptOnly = false)
    {
        $this->push = $push;
        $this->scriptOnly = $scriptOnly;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        return view('toolbox::recaptcha.checkbox');
    }
}
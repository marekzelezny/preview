<?php

namespace Adity\Mailer;

use eftec\bladeone\BladeOne;

class Template
{
    protected string $template;
    protected array $data;

    public function __construct(string $view, array $with)
    {
        $this->template = "emails.{$view}";
        $this->data = $with;
    }

    public function render() : string
    {
        $viewsFolder = dirname(__DIR__) . '/Views';
        $cacheFolder = WP_CONTENT_DIR . '/cache/adity/blade/emails';
        $blade = new BladeOne($viewsFolder, $cacheFolder, BladeOne::MODE_DEBUG);

        return $blade->run($this->template, $this->data);
    }
}

<?php

namespace App\Extensions\Ecomail;

use Ecomail as Ecomail;

class EcomailApi
{
    private Ecomail $ecomail;

    public function __construct()
    {
        $this->ecomail = new Ecomail(config('services.ecomail.apiKey'));
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        } elseif (method_exists($this->ecomail, $name)) {
            $class = $this->ecomail;

            return $class->{$name}(...$arguments);
        }

        return null;
    }
}

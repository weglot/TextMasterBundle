<?php

namespace Weglot\TextMasterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Weglot\TextMasterBundle\DependencyInjection\WeglotTextMasterExtension;

class WeglotTextMasterBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new WeglotTextMasterExtension();
    }
}

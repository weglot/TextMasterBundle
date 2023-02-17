<?php

namespace Weglot\TextMasterBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Weglot\TextMasterBundle\DependencyInjection\WeglotTextMasterExtension;

class WeglotTextMasterBundle extends Bundle
{
    public function getContainerExtension(): ExtensionInterface
    {
        return new WeglotTextMasterExtension();
    }
}

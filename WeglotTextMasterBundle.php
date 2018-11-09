<?php
/**
 * Created by PhpStorm.
 * User: etienne
 * Date: 29/10/2018
 * Time: 11:30.
 */

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

<?php

declare(strict_types=1);

namespace Jupi007\PrestashopWebserviceExtra;

use PrestaShopWebservice as PrestaShopWebserviceLib;

final class PrestashopWebservice extends PrestaShopWebserviceLib
{
    public function getUrl(): string
    {
        return $this->url;
    }

    public function getKey(): string
    {
        return $this->key;
    }
    
    public function getDebug(): bool
    {
        return $this->debug;
    }
}

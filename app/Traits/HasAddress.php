<?php

declare(strict_types=1);

namespace App\Traits;

use App\ValueObjects\Address;

/**
 * Exposes the address columns of the model as an `Address` through the `address` attribute.
 *
 * @property Address $address
 */
trait HasAddress
{
    public function initializeHasAddress(): void
    {
        $this->mergeCasts(['address' => Address::class]);
    }
}

<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Order;
use RuntimeException;

/**
 * An order with an active invoice was changed outside the invoicing flow.
 */
class InvoicedOrderException extends RuntimeException
{
    public static function for(Order $order): self
    {
        return new self($order->invoicedMessage() ?? 'The order is invoiced');
    }
}

<?php

declare(strict_types=1);

namespace App\ValueObject;

final class TicketStatus
{
    public const REGISTERED = 'registered';
    public const PAID = 'paid';
    public const CANCELED = 'canceled';
}

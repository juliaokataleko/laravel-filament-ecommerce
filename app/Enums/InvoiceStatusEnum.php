<?php

namespace App\Enums;

enum InvoiceStatusEnum: string
{
    case PAID = 'paid';
    case PARTIAL = 'partial';
    case TO_PAY = 'to_pay';
}

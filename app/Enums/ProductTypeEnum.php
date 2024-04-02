<?php

namespace App\Enums;

enum ProductTypeEnum : string
{
    case DELIVERABLE = 'deliverable';
    case DOWNLOADABLE = 'downloadable';
    case PRODUCT = 'product';
    case SERVICE = 'service';
}

<?php

namespace App\Constant;

class StatusConstant
{

    const   ORDER_PLACED = '1';
    CONST   ORDER_SHIPPED = '2';
    CONST   ORDER_CANCEL = '3';

    public static array $orderStatus = [
        self::ORDER_PLACED => 'ORDER PLACED',
        self::ORDER_SHIPPED => 'ORDER SHIPPED',
        self::ORDER_CANCEL => 'ORDER CANCELED',
    ];

    const   NEW_ORDER = '0';
    const   APPROVED = '1';
    const   UNAPPROVED = '2';

    public static array $approvedStatus = [
        self::NEW_ORDER     => 'NEW ORDER',
        self::APPROVED      => 'APPROVED',
        self::UNAPPROVED    => 'REJECTED',
    ];

    const   PRODUCT_TYPE_TEN_SET = '1';
    const   PRODUCT_TYPE_EIGHT_SET = '2';
    const   PRODUCT_TYPE_INDIVIDUAL_JERSEY_SET = '3';
    const PRODUCT_TYPE_INDIVIDUAL_SHORT_SET = '4';
    const   PRODUCT_TYPE_UNDEFINED = '0';

    public static array $productType = [
        self::PRODUCT_TYPE_TEN_SET => 'Tense Set',
        self::PRODUCT_TYPE_EIGHT_SET => 'Eight Set',
        self::PRODUCT_TYPE_INDIVIDUAL_JERSEY_SET => 'Individual Jersey Set',
        self::PRODUCT_TYPE_UNDEFINED => 'Undefined',
        self::PRODUCT_TYPE_INDIVIDUAL_SHORT_SET => 'Individual Short Set',
    ];

}

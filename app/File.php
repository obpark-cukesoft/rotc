<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public const SOURCE_TYPE_MEMBER_PHOTO = 1;
    public const SOURCE_TYPE_MEMBER_BUSINESS_CARD = 2;
    public const SOURCE_TYPES = [
        self::SOURCE_TYPE_MEMBER_PHOTO => 'member_photo',
        self::SOURCE_TYPE_MEMBER_BUSINESS_CARD => 'member_business_card',
    ];

}

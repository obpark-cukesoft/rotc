<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    //
    const LEVEL = 10;

    protected $with = ['photo','business_card'];
    protected $appends = ['status_text'];
    protected $hidden = ['password','gps'];

    public function getStatusTextAttribute()
    {
        return ($this->attributes['status']) ? User::STATUSES[$this->attributes['status']]: '';
    }

    public function photo()
    {
        return $this->hasOne('App\File', 'source_id', 'id')
            ->where('source_type', File::SOURCE_TYPE_MEMBER_PHOTO);
    }

    public function business_card()
    {
        return $this->hasOne('App\File', 'source_id', 'id')
            ->where('source_type', File::SOURCE_TYPE_MEMBER_BUSINESS_CARD);
    }
}

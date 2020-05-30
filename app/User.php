<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const LEVEL_ADMIN  = 1;
    const LEVEL_MEMBER = 10;

    const STATUS_NORMAL = 'N';
    const STATUS_REGISTER = 'R';
    const STATUS_STOP = 'S';
    const STATUSES = [self::STATUS_NORMAL => '정상', self::STATUS_REGISTER => '등록', self::STATUS_STOP => '중지'];
    const STATUS_ITEMS = [
        ['value' => self::STATUS_NORMAL, 'text' => '정상'],
        ['value' => self::STATUS_REGISTER, 'text' => '등록'],
        ['value' => self::STATUS_STOP, 'text' => '중지']
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin() {
        if (Auth::user()->level === USER::LEVEL_ADMIN) return true;
        return false;
    }

    public function isMember() {
        if (Auth::user()->level === USER::LEVEL_MEMBER) return true;
        return false;
    }

    /*
    public function findAndValidateForPassport($username, $password)
    {
        return $this->where('email', $username)->where('status', User::STATUS_NORMAL)->first();
    }

    public function findForPassport($username)
    {
        return $this->where('email', $username)->where('status', User::STATUS_NORMAL)->first();
    }

    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }
    */

}

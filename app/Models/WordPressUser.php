<?php

namespace App\Models;

use Corcel\Model\User as Corcel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class WordPressUser extends Corcel implements Authenticatable
{
    use AuthenticatableTrait;

    protected $connection = 'wordpress';
    protected $table = 'users';
    protected $primaryKey = 'ID'; // WordPress uses uppercase 'ID'
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // WordPress users table doesn't have Laravel timestamps

    protected $fillable = [
        'user_login',
        'user_email',
        'user_pass',
        'user_nicename',
        'display_name',
    ];

    protected $hidden = [
        'user_pass',
        'remember_token',
    ];

    // CRITICAL: Override these methods for Laravel auth
    public function getAuthIdentifierName()
    {
        return 'ID'; // WordPress primary key
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute('ID');
    }

    public function getAuthPassword()
    {
        return $this->user_pass;
    }

    public function getRememberToken()
    {
        return $this->remember_token ?? null;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // Add this method to ensure proper key retrieval
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    public function getKeyName()
    {
        return 'ID';
    }

    // WordPress-specific methods
    public function hasRole($role)
    {
        $capabilities = $this->meta->capabilities ?? [];
        return array_key_exists($role, $capabilities);
    }

    public function getRole()
    {
        $capabilities = $this->meta->capabilities ?? [];
        $roles = array_keys(array_filter($capabilities, function($val) {
            return $val === true;
        }));

        return !empty($roles) ? $roles[0] : 'subscriber';
    }
}

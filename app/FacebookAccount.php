<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacebookAccount extends Model
{
    protected $table = 'facebook_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'account_id', 'link', 'access_token'];

    public function user()
    {
        return $this->belongsTo('User');
    }
}

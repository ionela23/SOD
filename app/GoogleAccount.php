<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleAccount extends Model
{
    protected $table = 'google_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'access_token'];

    public function user()
    {
        return $this->belongsTo('User');
    }
}

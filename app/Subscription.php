<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
	protected $table = 'subscriptions';
	protected $fillable = ['harvest_id', 'subcription_id', 'plan_id', 'user_id', 'created_at', 'updated_at'];
	public $timestamps = true;
}

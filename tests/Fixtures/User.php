<?php

namespace Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ladder\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
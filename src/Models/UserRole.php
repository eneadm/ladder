<?php

namespace Ladder\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    public $incrementing = true;
}
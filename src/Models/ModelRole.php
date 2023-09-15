<?php

namespace Ladder\Models;

use Database\Factories\ModelRoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ModelRole extends Pivot
{
    use HasFactory;

    public $incrementing = true;

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    protected static function newFactory(): ModelRoleFactory
    {
        return new ModelRoleFactory();
    }
}

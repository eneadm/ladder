<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ladder\HasRoles;

class Folder extends Model
{
    use HasFactory;
    use HasRoles;
}

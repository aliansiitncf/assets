<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageSetup extends Model
{
    use HasFactory;
    protected $table = 'page_setups';
    protected $primaryKey = 'id_page_setup';
    protected $guarded = ['id_page_setup'];
}

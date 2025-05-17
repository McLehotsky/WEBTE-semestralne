<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfEdit extends Model
{
    protected $table = 'pdf_edits';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function editHistories()
    {
        return $this->hasMany(EditHistory::class);
    }
}

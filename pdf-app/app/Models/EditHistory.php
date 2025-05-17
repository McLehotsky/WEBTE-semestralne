<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditHistory extends Model
{
    protected $table = 'edit_history';

    protected $fillable = [
        'user_id',
        'pdf_edit_id',
        'accessed_via',
        'used_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pdfEdit()
    {
        return $this->belongsTo(PdfEdit::class);
    }
}

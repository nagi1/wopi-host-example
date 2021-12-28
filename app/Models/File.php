<?php

namespace App\Models;

use Sushi\Sushi;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use Sushi;

    protected $guarded = [];

    protected $schema = [
        'id' => 'integer',
        'name' => 'string',
        'path' => 'string',
        'lock' => 'string',
        'size' => 'integer',
        'hash' => 'text',
        'version' => 'string',
        'extension' => 'string',
        'user_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    protected $rows = [
        [
            // for testing
            'id' => 1,
            'name' => 'test.wopitest',
            'path' => 'test.wopitest', // relative to public driver
            'lock' => '',
            'size' => 0,
            'hash' => '',
            'version' => '1',
            'extension' => 'wopitest',
            'user_id' => 1,
        ],

        [
            // docx
            'id' => 2,
            'name' => 'test.docx',
            'path' => 'test.docx', // relative to public driver
            'lock' => '',
            'size' => 16183,
            'hash' => '',
            'version' => '1',
            'extension' => 'docx',
            'user_id' => 1,
        ],

        [

            // pptx
            'id' => 3,
            'name' => 'test.pptx',
            'path' => 'test.pptx', // relative to public driver
            'lock' => '',
            'size' => 626332,
            'hash' => '',
            'version' => '1',
            'extension' => 'pptx',
            'user_id' => 1,

        ],
        [
            // xlsx
            'id' => 4,
            'name' => 'test.xlsx',
            'path' => 'test.xlsx', // relative to public driver
            'lock' => '',
            'size' => 83418,
            'hash' => '',
            'version' => '1',
            'extension' => 'xlsx',
            'user_id' => 1,
        ]
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

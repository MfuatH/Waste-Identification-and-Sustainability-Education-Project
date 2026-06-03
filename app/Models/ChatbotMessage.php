<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender',
        'message'
    ];

    public function conversation()
    {
        return $this->belongsTo(ChatbotConversation::class);
    }
}
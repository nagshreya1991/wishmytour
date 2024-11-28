<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    
    protected $fillable = ['receiver_id', 'message', 'read'];
    public static function markAllAsRead($receiverId)
    {
        self::where('receiver_id', $receiverId)->where('read', false)->update(['read' => true]);
    }
    public function markAsRead()
    {
        $this->read = true;
        $this->save();
    }

    public function markAsUnread()
    {
        $this->read = false;
        $this->save();
    }

    public static function getByReceiverId($receiverId, $read = null)
    {
        $query = self::where('receiver_id', $receiverId);
        
        if ($read !== null) {
            $query->where('read', $read);
        }

        return $query->get();
    }

    public static function unreadNotificationsCount($receiverId)
    {
        return self::where('receiver_id', $receiverId)->where('read', false)->count();
    }
}
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public $tries = 3; // Số lần thử lại nếu fail
    public $timeout = 30; // Thời gian tối đa cho mỗi lần thử (giây)

    public $user;
    public $oldRole;
    public $oldStatus;

    public function __construct(User $user, $oldRole, $oldStatus)
    {
        $this->user = $user;
        $this->oldRole = $oldRole;
        $this->oldStatus = $oldStatus;
    }    public function build()
    {
        $subject = 'Thông báo thay đổi ';
        if ($this->user->role->name !== $this->oldRole && $this->user->status !== $this->oldStatus) {
            $subject .= 'vai trò và trạng thái tài khoản';
        } elseif ($this->user->role->name !== $this->oldRole) {
            $subject .= 'vai trò tài khoản';
        } else {
            $subject .= 'trạng thái tài khoản';
        }
        
        return $this->subject($subject)
                    ->view('emails.user-status-updated');
    }
}
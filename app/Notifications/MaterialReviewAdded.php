<?php

namespace App\Notifications;

use App\User;
use App\Models\Material;
use App\Models\MaterialReview;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MaterialReviewAdded extends Notification
{
    use Queueable;

    protected $material = null;
    protected $user = null;
    protected $review = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Material $material, User $user, MaterialReview $review)
    {
        $this->material = $material;
        $this->user = $user;
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('A user reviewed on one of your recipes.')
                    ->action('View Comment', url('/recipes/'.$this->material->id))
                    ->line('Thank you for using Glazy!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $name = $this->user->name;
        if (array_key_exists('username', $this->user) && $this->user->username) {
            $name = $this->user->username;
        }
        $title = $name.' reviewed '.$this->material->name;
        $message = $this->review->content;
        $link = '/recipes/'.$this->material->id.'#reviews';

        return [
            'material_id' => $this->material->id,
            'user_id' => $this->user->id,
            'type' => 'comment',
            'review_id' => $this->review->id,
            'title' => $title,
            'message' => $message,
            'link' => $link
        ];
    }
}


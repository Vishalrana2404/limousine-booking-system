<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Http\Resources\AjaxResource;
use Auth;

class SendNotification extends Notification
{
    use Queueable;
    protected $type, $data, $template, $message, $from, $notificationType, $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($setting)
    {
        $this->type = $setting['type'];
        $this->subject = $setting['subject'] ?? null;
        $this->from = $setting['from'];
        $this->data = $setting['data'];
        $this->template = $setting['template'];
        $this->message = $setting['message'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [ 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = $this->data;
        $subject = $this->subject;
        return (new MailMessage)
            ->subject($subject)
            ->markdown($this->template, compact('data', 'notifiable'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'from' => $this->from,
            'data' => $this->data,
            'template' => $this->template,
            'message' => $this->message,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $resource = new AjaxResource([
            'id' => $this->id,
            'from' => $this->from,
            'data' => $this->data,
            'template' => $this->template,
            'message' => $this->message,
        ]);
        $resource->setStatusCode(200);
        $resource = (array) $resource;
        return new BroadcastMessage($resource);
    }

    
}

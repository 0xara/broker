<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class AlertActivatedNotification extends Notification
{
    use Queueable;

    /** @var Alert  */
    public $alert;

    const OPERATORS = [
        Alert::GTE => ' > ',
        Alert::LTE => ' < ',
    ];

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class, DiscordChannel::class];
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
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
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
            //
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->to($notifiable->slack_username)
            ->content("Alert: ".$this->alert->symbol." is ".Alert::OPERATOR_TITLES[$this->alert->operator]." ".$this->alert->price);
    }

    public function toTelegram($notifiable)
    {
        $content = "\n #".$this->alert->symbol.self::OPERATORS[$this->alert->operator].((float) $this->alert->price);
        $content .= ($this->alert->details ? "\n \n {$this->alert->details}" : '');
        $content .= is_array($this->alert->charts) && count($this->alert->charts) ?  "\n \n \n".$this->alert->charts[0] : '';

        return TelegramMessage::create()
            ->to($notifiable->telegram_user_id)
            ->content($content);
    }

    public function toDiscord($notifiable)
    {
        $content = "\n #".$this->alert->symbol.self::OPERATORS[$this->alert->operator].((float) $this->alert->price);
        $content .= ($this->alert->details ? "\n \n {$this->alert->details}" : '');
        $content .= is_array($this->alert->charts) && count($this->alert->charts) ?  "\n \n \n".$this->alert->charts[0] : '';

        return DiscordMessage::create($content);
    }
}

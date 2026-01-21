<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class WelcomeCredentials extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $password,
        public Tenant $tenant
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Your Account Credentials')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Welcome to ' . config('app.name') . '! Your account has been successfully created for **' . $this->tenant->name . '**.')
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Password:** ' . $this->password)
            ->line('Please verify your email address by clicking the button below:')
            ->action('Verify Email Address', $verificationUrl)
            ->line('After verification, you can log in and change your password from your profile settings.')
            ->line('For security reasons, we recommend changing your password after your first login.')
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Thank you for choosing ' . config('app.name') . '!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'password' => $this->password,
            'tenant_id' => $this->tenant->id,
        ];
    }
}
            //
        ];
    }
}

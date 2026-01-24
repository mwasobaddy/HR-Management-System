<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
        $loginUrl = $this->generateTenantSignedLoginUrl($notifiable);

        return (new MailMessage)
            ->subject('Welcome to '.config('app.name').' - Your Account is Ready')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('Welcome to '.config('app.name').'! Your account has been successfully created for **'.$this->tenant->company_name.'**.')
            ->line('Here are your login credentials:')
            ->line('**Email:** '.$notifiable->email)
            ->line('**Password:** '.$this->password)
            ->line('**Domain:** '.$this->tenant->domains()->first()?->domain)
            ->line('Click the button below to log in to your account:')
            ->action('Log In to Your Account', $loginUrl)
            ->line('For security reasons, we recommend changing your password after your first login.')
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Thank you for choosing '.config('app.name').'!');
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

    /**
     * Generate a signed login URL that targets the tenant's domain (including local prefix/port when configured).
     */
    protected function generateTenantSignedLoginUrl(object $notifiable): string
    {
        $tenantRootUrl = $this->tenantBaseUrl();
        $expiration = now()->addMinutes(60);

        if (! $tenantRootUrl) {
            return URL::temporarySignedRoute('auth.login', $expiration, [
                'user_id' => $notifiable->getKey(),
            ]);
        }

        $originalAppUrl = config('app.url');

        try {
            URL::forceRootUrl($tenantRootUrl);
            config(['app.url' => $tenantRootUrl]);

            return URL::temporarySignedRoute('auth.login', $expiration, [
                'user_id' => $notifiable->getKey(),
            ]);
        } finally {
            // Restore original root for subsequent URL generation
            URL::forceRootUrl($originalAppUrl);
            config(['app.url' => $originalAppUrl]);
        }
    }

    /**
     * Build the base URL (scheme + host + optional port) for the tenant.
     */
    protected function tenantBaseUrl(): ?string
    {
        $domain = $this->tenant->domains()->first()?->domain;

        if (! $domain) {
            return null;
        }

        $scheme = config('tenancy.tenant_url_scheme', 'http');

        $configuredPort = config('tenancy.tenant_url_port');
        $fallbackPort = parse_url(config('app.url'), PHP_URL_PORT);
        $port = $configuredPort ?: $fallbackPort;

        $host = trim($domain);
        $portSegment = $port ? ':'.$port : '';

        return Str::finish($scheme.'://'.$host.$portSegment, '');
    }
}

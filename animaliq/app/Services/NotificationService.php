<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create in-app notifications for all users and queue emails.
     *
     * @param string   $type      program|event|post|research
     * @param string   $title     Notification title
     * @param string   $body      Short description
     * @param string   $url       Link to the content
     * @param callable $mailer    fn(User $user): Mailable  — returns a ready Mailable
     */
    public function broadcast(string $type, string $title, string $body, string $url, callable $mailer): void
    {
        User::whereNotNull('email')->orderBy('id')->chunkById(100, function ($users) use ($type, $title, $body, $url, $mailer) {
            foreach ($users as $user) {
                try {
                    Notification::create([
                        'user_id' => $user->id,
                        'type'    => $type,
                        'title'   => $title,
                        'body'    => $body,
                        'url'     => $url,
                    ]);
                    Mail::to($user->email)->queue($mailer($user));
                } catch (\Throwable $e) {
                    \Log::error('Notification broadcast failed for user '.$user->id.': '.$e->getMessage());
                }
            }
        });
    }
}

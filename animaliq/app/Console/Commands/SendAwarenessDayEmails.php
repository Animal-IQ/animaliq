<?php

namespace App\Console\Commands;

use App\Mail\AwarenessDayNotification;
use App\Models\AwarenessDay;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendAwarenessDayEmails extends Command
{
    protected $signature = 'animaliq:awareness-day-emails
                            {--force : Send even if already notified today}';

    protected $description = 'Send awareness day emails to users for today\'s annual celebrations (Africa/Nairobi calendar day)';

    public function handle(): int
    {
        $today = AwarenessDay::celebrationToday();
        $this->info('Checking awareness days for ' . $today->toDateString() . ' (' . AwarenessDay::celebrationTimezone() . ').');

        $todayDays = AwarenessDay::active()->today()->get();

        if ($todayDays->isEmpty()) {
            $this->info('No awareness days for today.');
            return self::SUCCESS;
        }

        $users = User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->when(
                \Illuminate\Support\Facades\Schema::hasColumn('users', 'status'),
                fn ($q) => $q->where(function ($q2) {
                    $q2->where('status', 'active')->orWhereNull('status');
                })
            )
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users with email addresses found.');
            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $sent = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($todayDays as $day) {
            $this->info("Sending for: {$day->title}");

            foreach ($users as $user) {
                if (! $force && $this->alreadyNotifiedToday($user->id, $day)) {
                    $skipped++;
                    continue;
                }

                $title = 'Today is ' . $day->title . '!';
                $body = $day->body ? Str::limit(strip_tags($day->body), 100) : '';

                try {
                    Mail::to($user->email)->send(
                        new AwarenessDayNotification($day, $user->first_name ?: 'there')
                    );

                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'awareness_day',
                        'title' => $title,
                        'body' => $body,
                        'url' => route('awareness-days.index'),
                    ]);

                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::error('Awareness day email failed', [
                        'email' => $user->email,
                        'day_id' => $day->id,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("Failed for {$user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Done. Sent: {$sent}, skipped (already sent today): {$skipped}, failed: {$failed}.");

        return $failed > 0 && $sent === 0 ? self::FAILURE : self::SUCCESS;
    }

    protected function alreadyNotifiedToday(int $userId, AwarenessDay $day): bool
    {
        $today = AwarenessDay::celebrationToday();
        $startUtc = $today->copy()->startOfDay()->utc();
        $endUtc = $today->copy()->endOfDay()->utc();

        return Notification::query()
            ->where('user_id', $userId)
            ->where('type', 'awareness_day')
            ->where('title', 'Today is ' . $day->title . '!')
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->exists();
    }
}

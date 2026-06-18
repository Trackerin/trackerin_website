<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'google_id', 'email_verified_at', 'profile_image', 'last_login_at', 'total_study_time', 'current_streak', 'last_active_date', 'weekly_activity'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'last_active_date' => 'date',
            'weekly_activity' => 'array',
        ];
    }

    public function syncStreakAndActivity()
    {
        $today = \Carbon\Carbon::today();
        $yesterday = \Carbon\Carbon::yesterday();

        $weekly = $this->weekly_activity;
        $currentYear = $today->year;
        $currentWeek = $today->weekOfYear;

        // Check if week changed or empty
        if (empty($weekly) || !isset($weekly['year']) || !isset($weekly['week']) || $weekly['year'] != $currentYear || $weekly['week'] != $currentWeek) {
            $weekly = [
                'year' => $currentYear,
                'week' => $currentWeek,
                'MON' => -1,
                'TUE' => -1,
                'WED' => -1,
                'THU' => -1,
                'FRI' => -1,
                'SAT' => -1,
                'SUN' => -1
            ];
        }

        // Sync Streak
        if (!$this->last_active_date) {
            $this->last_active_date = $today;
            $this->current_streak = 1;
        } else {
            $lastActive = \Carbon\Carbon::parse($this->last_active_date)->startOfDay();
            if ($lastActive->equalTo($yesterday)) {
                $this->last_active_date = $today;
                $this->current_streak += 1;
            } elseif (!$lastActive->equalTo($today)) {
                $this->last_active_date = $today;
                $this->current_streak = 1;
            }
        }

        // Ensure today's activity is at least 0 (active today)
        $todayDay = strtoupper($today->shortEnglishDayOfWeek); // e.g. "MON"
        if (isset($weekly[$todayDay]) && $weekly[$todayDay] < 0) {
            $weekly[$todayDay] = 0;
        }

        $this->weekly_activity = $weekly;
        $this->save();
    }

    public function incrementDailyActivity($amount = 20)
    {
        $this->syncStreakAndActivity();
        
        $today = \Carbon\Carbon::today();
        $todayDay = strtoupper($today->shortEnglishDayOfWeek);
        
        $weekly = $this->weekly_activity;
        $current = $weekly[$todayDay] ?? -1;
        $base = $current < 0 ? 0 : $current;
        $weekly[$todayDay] = min($base + $amount, 100);
        
        $this->weekly_activity = $weekly;
        $this->save();
    }

    public function curriculums(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}

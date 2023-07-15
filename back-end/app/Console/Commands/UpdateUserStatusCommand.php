<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vacation;
use App\Models\Secondment;
use App\Models\Legation;
use Illuminate\Console\Command;

class UpdateUserStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user-status:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates user status based on vacations, legations, and secondments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->handleVacations();
        $this->handleLegations();
        $this->handleSecondments();
    }

    private function handleVacations()
    {
        $today = now()->toDateString();
        $vacations = Vacation::where(function ($query) use ($today) {
            $query->where('status', 'Accepted') // Only fetch accepted vacations
                  ->where(function ($query) use ($today) {
                      $query->whereDate('start_date', '=', $today)
                            ->orWhereDate('end_date', '=', $today);
                  });
        })->get();

        foreach ($vacations as $vacation) {
            $user = User::find($vacation->user_id);
            if ($user) {
                // If the start date is today and the user is not active,
                if ($vacation->start_date == $today && !$user->is_active) {
                    continue;
                }
                // If the end date is today and the user is not active,
                elseif ($vacation->end_date == $today && !$user->is_active) {
                    $user->is_active = true;
                    $user->reason = null;
                    $user->save();
                }
                // If the start date is today and the user is active,
                elseif ($vacation->start_date == $today && $user->is_active) {
                    $user->is_active = false;
                    $user->reason = 'Vacation';
                    $user->save();
                }
            }
        }
    }

    private function handleLegations()
    {
        $today = now()->toDateString();
        $legations = Legation::where(function ($query) use ($today) {
            $query->where('status', 'Accepted') // Only fetch accepted legations
                  ->where(function ($query) use ($today) {
                      $query->whereDate('start_date', '=', $today)
                            ->orWhereDate('end_date', '=', $today);
                  });
        })->get();

        foreach ($legations as $legation) {
            $user = User::find($legation->user_id);
            if ($user) {
                // If the start date is today and the user is not active,
                if ($legation->start_date == $today && !$user->is_active) {
                    continue;
                }
                // If the end date is today and the user is not active,
                elseif ($legation->end_date == $today && !$user->is_active) {
                    $user->is_active = true;
                    $user->reason = null;
                    $user->save();
                }
                // If the start date is today and the user is active,
                elseif ($legation->start_date == $today && $user->is_active) {
                    $user->is_active = false;
                    $user->reason = 'Legation';
                    $user->save();
                }
            }
        }
    }

    private function handleSecondments()
    {
        $today = now()->toDateString();
        $secondments = Secondment::where(function ($query) use ($today) {
            $query->where('status', 'Accepted') // Only fetch accepted secondments
                  ->where(function ($query) use ($today) {
                      $query->whereDate('start_date', '=', $today)
                            ->orWhereDate('end_date', '=', $today);
                  });
        })->get();

        foreach ($secondments as $secondment) {
            $user = User::find($secondment->user_id);
            if ($user) {
                // If the start date is today and the user is not active,
                if ($secondment->start_date == $today && !$user->is_active) {
                    continue;
                }
                // If the end date is today and the user is not active,
                elseif ($secondment->end_date == $today && !$user->is_active) {
                    $user->is_active = true;
                    $user->reason = null;
                    $user->save();
                }
                // If the start date is today and the user is active,
                elseif ($secondment->start_date == $today && $user->is_active) {
                    $user->is_active = false;
                    $user->reason = 'Secondment';
                    $user->save();
                }
            }
        }
    }
}
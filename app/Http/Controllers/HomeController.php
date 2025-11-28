<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Image;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\ScheduleExecutionLog;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $customersCount = Customer::count();
        $imagesCount = Image::count();
        $schedulesCount = Schedule::count();
        $unreadNotificationsCount = Notification::where('is_read', false)->count();
        $notifications = Notification::orderBy('created_at', 'desc')->limit(5)->get();

        $imagesSentToday = ScheduleExecutionLog::whereDate('execution_date', Carbon::today())->count();
        $imagesSentYesterday = ScheduleExecutionLog::whereDate('execution_date', Carbon::yesterday())->count();

        $recentExecutionLogs = ScheduleExecutionLog::with('schedule')
            ->orderBy('execution_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                $executionCarbon = null;
                try {
                    $executionCarbon = Carbon::parse($log->execution_date);
                } catch (\Exception $e) {
                    $executionCarbon = null;
                }

                return [
                    'id' => $log->id,
                    'schedule_id' => $log->schedule_id,
                    'schedule_time' => optional($log->schedule)->time,
                    'status' => $log->status,
                    'error_message' => $log->error_message,
                    'execution_date' => $executionCarbon ? $executionCarbon->toDateTimeString() : null,
                    'execution_time' => $executionCarbon ? $executionCarbon->format('H:i:s') : null,
                ];
            });

        return inertia('Home/Index', [
            'customersCount' => $customersCount,
            'imagesCount' => $imagesCount,
            'schedulesCount' => $schedulesCount,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'notifications' => $notifications,
            'imagesSentToday' => $imagesSentToday,
            'imagesSentYesterday' => $imagesSentYesterday,
            'recentExecutionLogs' => $recentExecutionLogs,
        ]);
    }
}

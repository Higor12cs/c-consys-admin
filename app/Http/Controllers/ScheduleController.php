<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateImageJob;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::all();

        return inertia('Indicators/Schedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    public function create()
    {
        return inertia('Indicators/Schedules/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'time' => 'required|string|max:255',
        ], [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser do tipo texto.',
            'max' => 'O campo deve ter no máximo :max caracteres.',
        ]);

        Schedule::create($validated);

        return to_route('indicators.schedules.index')->with('success', 'Agendamento criado com sucesso!');
    }

    public function edit(Schedule $schedule)
    {
        return inertia('Indicators/Schedules/Edit', [
            'schedule' => $schedule,
        ]);
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'time' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ], [
            'required' => 'O campo é obrigatório.',
            'string' => 'O campo deve ser do tipo texto.',
            'max' => 'O campo deve ter no máximo :max caracteres.',
            'boolean' => 'O campo deve ser verdadeiro ou falso.',
        ]);

        $schedule->update($validated);

        return to_route('indicators.schedules.index')->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function resend(Schedule $schedule)
    {
        $images = $schedule->images()->with('customer')->where('is_active', true)->get();

        foreach ($images as $image) {
            $destinations = collect($image->destinations)->map(function ($destination, $index) {
                return [
                    'value' => $destination['value'],
                    'delay' => $index * 5,
                ];
            })->toArray();

            \Log::info("Resending job for Image ID: {$image->id} | Customer: {$image->customer->name} | Under Schedule ID: {$schedule->id}");

            GenerateImageJob::dispatch($image, $schedule->id, $destinations);
        }

        return to_route('indicators.schedules.index')->with('success', 'Reenvio de imagens agendado com sucesso!');
    }
}

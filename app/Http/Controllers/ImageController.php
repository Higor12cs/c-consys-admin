<?php

namespace App\Http\Controllers;

use App\Enums\ChartType;
use App\Enums\DestinationType;
use App\Models\Customer;
use App\Models\Image;
use App\Models\Indicator;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function index()
    {
        $images = Image::with('customer')->get();

        return inertia('Indicators/Images/Index', compact('images'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $schedules = Schedule::orderBy('time')->get();
        $chartTypes = ChartType::options();
        $destinationTypes = DestinationType::options();
        $indicators = Indicator::orderBy('code')->get()->map(function ($i) {
            return ['value' => $i->code, 'label' => $i->code.' - '.$i->description];
        })->toArray();

        return inertia(
            'Indicators/Images/Create',
            compact('customers', 'schedules', 'chartTypes', 'destinationTypes', 'indicators')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'indicators' => 'nullable|array',
            'indicators.*.code' => 'required|string',
            'indicators.*.position' => 'required|integer|min:0|max:999',
            'charts' => 'nullable|array',
            'charts.*.type' => 'required|string',
            'charts.*.row' => 'required|integer|min:0|max:999',
            'charts.*.col' => 'required|integer|min:0|max:999',
            'destinations' => 'nullable|array',
            'destinations.*.type' => 'required|string|in:group,contact',
            'destinations.*.value' => 'required|string',
            'schedules' => 'nullable|array',
            'schedules.*.schedule_id' => 'required|exists:schedules,id',
            'schedules.*.sun' => 'boolean',
            'schedules.*.mon' => 'boolean',
            'schedules.*.tue' => 'boolean',
            'schedules.*.wed' => 'boolean',
            'schedules.*.thu' => 'boolean',
            'schedules.*.fri' => 'boolean',
            'schedules.*.sat' => 'boolean',
            'is_active' => 'required|boolean',
        ], [
            'required' => 'O campo é obrigatório.',
            'exists' => 'O campo deve existir.',
            'string' => 'O campo deve ser um texto.',
            'max' => 'O campo deve ter no máximo :max caracteres.',
            'array' => 'O campo deve ser um array.',
            'integer' => 'O campo deve ser um número inteiro.',
            'min' => 'O campo deve ter no mínimo :min.',
            'in' => 'O campo selecionado é inválido.',
            'boolean' => 'O campo deve ser verdadeiro ou falso.',
        ]);

        $image = Image::create([
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'company' => $data['company'],
            'indicators' => $data['indicators'] ?? [],
            'charts' => $data['charts'] ?? [],
            'destinations' => $data['destinations'] ?? [],
            'is_active' => $data['is_active'],
        ]);

        if (! empty($data['schedules'])) {
            foreach ($data['schedules'] as $schedule) {
                $image->schedules()->attach($schedule['schedule_id'], [
                    'sun' => $schedule['sun'] ?? false,
                    'mon' => $schedule['mon'] ?? true,
                    'tue' => $schedule['tue'] ?? true,
                    'wed' => $schedule['wed'] ?? true,
                    'thu' => $schedule['thu'] ?? true,
                    'fri' => $schedule['fri'] ?? true,
                    'sat' => $schedule['sat'] ?? false,
                ]);
            }
        }

        return redirect()->route('indicators.images.index')->with('success', 'Imagem criada com sucesso!');
    }

    public function edit(Image $image)
    {
        $image->load(['customer', 'schedules']);

        $customers = Customer::orderBy('name')->get();
        $schedules = Schedule::orderBy('time')->get();
        $chartTypes = ChartType::options();
        $destinationTypes = DestinationType::options();
        $indicators = Indicator::orderBy('code')->get()->map(function ($i) {
            return ['value' => $i->code, 'label' => $i->code.' - '.$i->description];
        })->toArray();

        return inertia(
            'Indicators/Images/Edit',
            compact('image', 'customers', 'schedules', 'chartTypes', 'destinationTypes', 'indicators')
        );
    }

    public function update(Request $request, Image $image)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'indicators' => 'nullable|array',
            'indicators.*.code' => 'required|string',
            'indicators.*.position' => 'required|integer|min:0|max:999',
            'charts' => 'nullable|array',
            'charts.*.type' => 'required|string',
            'charts.*.row' => 'required|integer|min:0|max:999',
            'charts.*.col' => 'required|integer|min:0|max:999',
            'destinations' => 'nullable|array',
            'destinations.*.type' => 'required|string|in:group,contact',
            'destinations.*.value' => 'required|string',
            'schedules' => 'nullable|array',
            'schedules.*.schedule_id' => 'required|exists:schedules,id',
            'schedules.*.sun' => 'boolean',
            'schedules.*.mon' => 'boolean',
            'schedules.*.tue' => 'boolean',
            'schedules.*.wed' => 'boolean',
            'schedules.*.thu' => 'boolean',
            'schedules.*.fri' => 'boolean',
            'schedules.*.sat' => 'boolean',
            'is_active' => 'required|boolean',
        ], [
            'required' => 'O campo é obrigatório.',
            'exists' => 'O campo deve existir.',
            'string' => 'O campo deve ser um texto.',
            'max' => 'O campo deve ter no máximo :max caracteres.',
            'array' => 'O campo deve ser um array.',
            'integer' => 'O campo deve ser um número inteiro.',
            'min' => 'O campo deve ter no mínimo :min.',
            'in' => 'O campo selecionado é inválido.',
            'boolean' => 'O campo deve ser verdadeiro ou falso.',
        ]);

        $image->update([
            'customer_id' => $data['customer_id'],
            'name' => $data['name'],
            'company' => $data['company'],
            'indicators' => $data['indicators'] ?? [],
            'charts' => $data['charts'] ?? [],
            'destinations' => $data['destinations'] ?? [],
            'is_active' => $data['is_active'],
        ]);

        $image->schedules()->detach();

        if (! empty($data['schedules'])) {
            foreach ($data['schedules'] as $schedule) {
                $image->schedules()->attach($schedule['schedule_id'], [
                    'sun' => $schedule['sun'] ?? false,
                    'mon' => $schedule['mon'] ?? true,
                    'tue' => $schedule['tue'] ?? true,
                    'wed' => $schedule['wed'] ?? true,
                    'thu' => $schedule['thu'] ?? true,
                    'fri' => $schedule['fri'] ?? true,
                    'sat' => $schedule['sat'] ?? false,
                ]);
            }
        }

        return redirect()->route('indicators.images.index')->with('success', 'Imagem atualizada com sucesso!');
    }

    public function destroy(Image $image)
    {
        $image->delete();

        return redirect()->route('indicators.images.index')->with('success', 'Imagem removida com sucesso!');
    }
}

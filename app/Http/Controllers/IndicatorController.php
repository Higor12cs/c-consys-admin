<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IndicatorController extends Controller
{
    public function index()
    {
        $indicators = Indicator::orderBy('code')->get();

        return inertia('Indicators/Index', [
            'indicators' => $indicators,
        ]);
    }

    public function create()
    {
        return inertia('Indicators/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|size:4|unique:indicators,code',
            'description' => 'required|string',
            'is_percentage' => 'nullable|boolean',
        ]);

        Indicator::create([
            'code' => $data['code'],
            'description' => $data['description'],
            'is_percentage' => ! empty($data['is_percentage']),
        ]);

        // Invalidate indicators cache so UI reflects new entries
        Cache::forget('indicators_map');

        return to_route('indicators.index')->with('success', 'Indicador criado com sucesso!');
    }

    public function edit(Indicator $indicator)
    {
        return inertia('Indicators/Edit', ['indicator' => $indicator]);
    }

    public function update(Request $request, Indicator $indicator)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'is_percentage' => 'nullable|boolean',
        ]);

        $indicator->update([
            'description' => $data['description'],
            'is_percentage' => ! empty($data['is_percentage']),
        ]);

        // Invalidate indicators cache after update
        Cache::forget('indicators_map');

        return to_route('indicators.index')->with('success', 'Indicador atualizado com sucesso!');
    }

    public function destroy(Indicator $indicator)
    {
        // If delete is enabled in the future, ensure cache is invalidated
        // $indicator->delete();
        Cache::forget('indicators_map');

        return to_route('indicators.index')->with('error', 'Função desabilitada!');
    }
}

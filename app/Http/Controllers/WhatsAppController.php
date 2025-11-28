<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function index(WhatsAppService $whatsAppService)
    {
        $result = $whatsAppService->listGroups();
        if (! empty($result['success'])) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error'] ?? 'Unknown'], $result['status'] ?? 500);
    }

    public function store(Request $request, WhatsAppService $whatsAppService)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'contacts' => 'sometimes|array',
        ]);

        $name = $data['name'];
        $contacts = $data['contacts'] ?? [];

        $result = $whatsAppService->createGroup($name, $contacts);
        if (! empty($result['success'])) {
            return response()->json($result['data']);
        }

        return response()->json(['error' => $result['error'] ?? 'Unknown'], $result['status'] ?? 500);
    }
}

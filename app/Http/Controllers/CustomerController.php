<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();

        return inertia('Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function create()
    {
        return inertia('Customers/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'external_id' => 'nullable|string',
            'name' => 'required|string',
        ], [
            'string' => 'O campo deve ser do tipo texto.',
            'required' => 'O campo é obrigatório.',
        ]);

        $validated['api_token'] = Str::random(60);

        Customer::create($validated);

        return to_route('customers.index')->with('success', 'Cliente criado com sucesso!');
    }

    public function show(Customer $customer)
    {
        //
    }

    public function edit(Customer $customer)
    {
        return inertia('Customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'external_id' => 'nullable|string',
            'name' => 'required|string',
        ], [
            'string' => 'O campo deve ser do tipo texto.',
            'required' => 'O campo é obrigatório.',
        ]);

        $customer->update($validated);

        return to_route('customers.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        //
    }

    public function regenerate(Customer $customer)
    {
        $customer->api_token = Str::random(60);
        $customer->save();

        return to_route('customers.edit', $customer)->with('success', 'Token de API regenerado com sucesso!');
    }
}

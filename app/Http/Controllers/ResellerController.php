<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use App\Traits\DocumentValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ResellerController extends Controller
{
    use DocumentValidation;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resellers = Reseller::orderBy('name')
            ->paginate(10)
            ->through(fn ($reseller) => [
                'id' => $reseller->id,
                'name' => $reseller->name,
                'document' => $reseller->document,
                'email' => $reseller->email,
                'commission_rate' => $reseller->commission_rate . '%'
            ])
            ->withQueryString();

        return Inertia::render('Resellers/Index', [
            'resellers' => $resellers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Resellers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document' => [
                'required',
                'string',
                Rule::unique('resellers'),
                function ($attribute, $value, $fail) {
                    if (!$this->validateDocument($value)) {
                        $fail('O documento informado não é válido.');
                    }
                },
            ],
            'email' => 'required|email|unique:resellers',
            'password' => 'required|min:8',
            'commission_rate' => 'required|numeric|min:0|max:100'
        ]);

        Reseller::create($data);

        return redirect()->route('resellers.index')
            ->with('success', 'Revendedor cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reseller $reseller)
    {
        return Inertia::render('Resellers/Edit', [
            'reseller' => [
                'id' => $reseller->id,
                'name' => $reseller->name,
                'document' => $reseller->document,
                'email' => $reseller->email,
                'commission_rate' => $reseller->commission_rate
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reseller $reseller)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document' => [
                'required',
                'string',
                Rule::unique('resellers')->ignore($reseller->id),
                function ($attribute, $value, $fail) {
                    if (!$this->validateDocument($value)) {
                        $fail('O documento informado não é válido.');
                    }
                },
            ],
            'email' => ['required', 'email', Rule::unique('resellers')->ignore($reseller->id)],
            'password' => 'nullable|min:8',
            'commission_rate' => 'required|numeric|min:0|max:100'
        ]);

        // Remove password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $reseller->update($data);

        return redirect()->route('resellers.index')
            ->with('success', 'Revendedor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reseller $reseller)
    {
        $reseller->delete();

        return redirect()->route('resellers.index')
            ->with('success', 'Revendedor removido com sucesso!');
    }
}

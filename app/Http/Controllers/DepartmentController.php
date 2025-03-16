<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Departments/Index', [
            'departments' => Department::all()
        ]);
    }

    public function create()
    {
        return Inertia::render('Departments/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')]
        ]);

        Department::create($validated);

        return redirect()->route('departments')->with('success', 'Departamento criado com sucesso!');
    }

    public function edit(Department $department)
    {
        return Inertia::render('Departments/Edit', [
            'department' => $department
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('departments')->ignore($department->id)]
        ]);

        $department->update($validated);

        return redirect()->route('departments')->with('success', 'Departamento atualizado com sucesso!');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments')->with('success', 'Departamento excluído com sucesso!');
    }
}

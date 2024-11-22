<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        return view('branches.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        // Validate and save the branch
        $branch = Branch::create($request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]));

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('branches.create')->with('success', 'Branch created successfully. Create another one.');
        }

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
        ]);

        $branch->update($validatedData);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        if (!auth()->user()->canManageBranches()) {
            abort(403, 'Unauthorized action.');
        }
        $branch->delete();

        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }
}

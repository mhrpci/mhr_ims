<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tool;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Tool::with('branch');

        if ($user->isBranchRestricted()) {
            $query->where('branch_id', $user->branch_id);
        }

        $tools = $query->get();
        return view('tools.index', compact('tools'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->canCreateTool()) {
            abort(403, 'Unauthorized action.');
        }

        $branches = $user->isBranchRestricted()
            ? Branch::where('id', $user->branch_id)->get()
            : Branch::all();

        return view('tools.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->canCreateTool()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'tool_name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255|unique:tools',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only create tools for your assigned branch.');
                    }
                },
            ],
        ]);

        Tool::create($validatedData);

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('tools.create')->with('success', 'Tool created successfully. Create another one.');
        }

        return redirect()->route('tools.index')->with('success', 'Tool created successfully.');
    }

    public function show(Tool $tool)
    {
        $user = Auth::user();

        if (!$user->canManageTool($tool)) {
            abort(403, 'Unauthorized access to this tool.');
        }

        return view('tools.show', compact('tool'));
    }

    public function edit(Tool $tool)
    {
        $user = Auth::user();

        if (!$user->canEditTool() || !$user->canManageTool($tool)) {
            abort(403, 'Unauthorized action.');
        }

        $branches = $user->isBranchRestricted()
            ? Branch::where('id', $user->branch_id)->get()
            : Branch::all();

        return view('tools.edit', compact('tool', 'branches'));
    }

    public function update(Request $request, Tool $tool)
    {
        $user = Auth::user();

        if (!$user->canEditTool() || !$user->canManageTool($tool)) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'tool_name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255|unique:tools,barcode,' . $tool->id,
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only update tools for your assigned branch.');
                    }
                },
            ],
        ]);

        $tool->update($validatedData);
        return redirect()->route('tools.index')->with('success', 'Tool updated successfully.');
    }

    public function destroy(Tool $tool)
    {
        $user = Auth::user();

        if (!$user->canDeleteTool() || !$user->canManageTool($tool)) {
            abort(403, 'Unauthorized action.');
        }

        $tool->delete();
        return redirect()->route('tools.index')->with('success', 'Tool deleted successfully.');
    }

    public function transferTool(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'barcode' => 'required|string|exists:tools,barcode',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only transfer tools to your assigned branch.');
                    }
                },
            ],
        ]);

        $tool = Tool::where('barcode', $validatedData['barcode'])->first();

        if (!$tool || !$user->canManageTool($tool)) {
            return response()->json(['error' => 'Tool not found or unauthorized'], 404);
        }

        $oldBranchId = $tool->branch_id;
        $newBranchId = $validatedData['branch_id'];

        $tool->branch_id = $newBranchId;
        $tool->save();

        // Fetch the old and new branch details
        $oldBranch = Branch::select('id', 'name')->find($oldBranchId);
        $newBranch = Branch::select('id', 'name')->find($newBranchId);

        $message = $oldBranchId === $newBranchId
            ? 'Tool stored in the same branch.'
            : 'Tool transferred to new branch successfully.';

        // Fetch all branches for the dropdown
        $allBranches = Branch::select('id', 'name')->get();

        return response()->json([
            'message' => $message,
            'tool' => [
                'id' => $tool->id,
                'name' => $tool->tool_name,
                'barcode' => $tool->barcode,
                'old_branch' => $oldBranch,
                'new_branch' => $newBranch,
            ],
            'branches' => $allBranches,
        ], 200);
    }
}

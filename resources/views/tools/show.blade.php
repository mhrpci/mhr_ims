@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Tool Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('tools.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Tools
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">{{ $tool->tool_name }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $tool->branch->name ?? 'Not Assigned' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created at:</th>
                                    <td>{{ $tool->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Last updated:</th>
                                    <td>{{ $tool->updated_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Tool Name:</th>
                                    <td>{{ $tool->tool_name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Barcode:</th>
                                    <td>{{ $tool->barcode }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if(Auth::user()->canEditTool() && Auth::user()->canManageTool($tool))
                        <a href="{{ route('tools.edit', $tool) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil me-2"></i>Edit Tool
                        </a>
                        @endif

                        @if(Auth::user()->canDeleteTool() && Auth::user()->canManageTool($tool))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteToolModal">
                            <i class="bi bi-trash me-2"></i>Delete Tool
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->canDeleteTool() && Auth::user()->canManageTool($tool))
<!-- Delete Tool Modal -->
<div class="modal fade" id="deleteToolModal" tabindex="-1" aria-labelledby="deleteToolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteToolModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tool?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('tools.destroy', $tool) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@extends('layouts.app')

@section('content')
    @include('layouts.partials._header', ['title' => __('Account categories')])

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('New category') }}</h5>
                    <form method="POST" action="{{ url('/account/category/save') }}">
                        @csrf
                        <input type="hidden" name="id" id="edit_id" value="">
                        <div class="mb-3">
                            <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                   required maxlength="80" class="form-control form-control-lg">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        <button type="button" id="btnCancel" class="btn btn-secondary d-none" onclick="AccountCategory.cancelEdit()">
                            {{ __('Cancel') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th class="text-center">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td class="text-center text-nowrap">
                                        <button onclick="AccountCategory.edit({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                                class="btn btn-xs btn-warning" title="{{ __('Edit') }}">
                                            <i class="las la-pen"></i>
                                        </button>
                                        <button onclick="AccountCategory.delete({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                                class="btn btn-xs btn-danger" title="{{ __('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">{{ __('No categories found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const AccountCategory = {
    edit: function(id, name) {
        document.getElementById('edit_id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('btnCancel').classList.remove('d-none');
        document.getElementById('name').focus();
    },
    cancelEdit: function() {
        document.getElementById('edit_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('btnCancel').classList.add('d-none');
    },
    delete: function(id, name) {
        if (confirm(`{{ __('Are you sure you want to delete') }} "${name}"?`)) {
            fetch(`/account/category/delete/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => window.location.reload());
        }
    }
};
</script>
@endpush

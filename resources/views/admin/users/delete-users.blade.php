<form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-white text-danger me-2">
        <i class="far fa-trash-alt me-1"></i> Delete
    </button>
</form>

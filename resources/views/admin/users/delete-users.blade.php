<form id="deleteUserForm-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST"
    style="display: inline-block;">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-sm btn-white text-danger me-2" onclick="confirmDelete('{{ $user->id }}')">
        <i class="far fa-trash-alt me-1"></i> Delete
    </button>
</form>


<script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            showCloseButton: true,
            timer: 10000,
            timerProgressBar: true,
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`deleteUserForm-${userId}`).submit();
            }
        });
    }
</script>

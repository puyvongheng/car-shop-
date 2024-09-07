document.querySelectorAll('.openDeleteModalBtn').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const deleteModal = document.getElementById('deleteModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        confirmBtn.setAttribute('href', `delete_model.php?id=${id}`);
        deleteModal.style.display = 'block';
    });
});

document.querySelectorAll('.closeModalBtn').forEach(button => {
    button.addEventListener('click', function() {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.style.display = 'none';
    });
});
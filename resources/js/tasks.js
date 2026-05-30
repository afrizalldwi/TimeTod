// Toggle task completion
window.toggleTask = async function (taskId) {
    try {
        const response = await fetch(`/tasks/${taskId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        });

        if (response.ok) {
            location.reload();
        }
    } catch (e) {
        console.error('Failed to toggle task:', e);
    }
};

// Edit task modal
window.editTask = function (id, title, priority, dueDate) {
    const modal = document.getElementById('editModal');
    const form = document.getElementById('editForm');
    document.getElementById('editTitle').value = title;
    document.getElementById('editPriority').value = priority;
    document.getElementById('editDueDate').value = dueDate;
    form.action = `/tasks/${id}`;
    modal.classList.remove('hidden');
};

window.closeEditModal = function () {
    document.getElementById('editModal').classList.add('hidden');
};

document.addEventListener('click', function (e) {
    const modal = document.getElementById('editModal');
    if (e.target === modal) {
        closeEditModal();
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});

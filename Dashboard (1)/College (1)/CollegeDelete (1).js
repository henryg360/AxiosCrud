document.addEventListener('DOMContentLoaded', () => {
    const deleteCollegeForm = document.getElementById('deleteCollegeForm');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    
    // Event listener for delete button
    confirmDeleteBtn.addEventListener('click', () => {
        const collegeId = document.getElementById('college_id').value;

        if (confirm('Are you sure you want to delete this college? This action cannot be undone.')) {
            axios.post('BackendDeleteCollege.php', {
                delete_id: collegeId,
                confirm_delete: true
            })
            .then(response => {
                alert(response.data.message);
                window.location.href = 'CollegeListings.php';
            })
            .catch(error => {
                console.error('Error deleting college:', error);
                alert('An error occurred while deleting the college.');
            });
        }
    });

    // Event listener for cancel button
    cancelBtn.addEventListener('click', () => {
        window.location.href = 'CollegeListings.php';
    });
});

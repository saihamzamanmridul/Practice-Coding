document.addEventListener('DOMContentLoaded', function() {
    // Auto dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    });

    // Confirm before delete
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            if (!confirm('Are you sure you want to delete this user?')) {
                e.preventDefault();
            }
        });
    });

    // Image preview for file uploads
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const preview = this.nextElementSibling;
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (!preview || !preview.classList.contains('image-preview')) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.width = 100;
                        img.classList.add('img-thumbnail', 'mt-2');
                        this.parentNode.insertBefore(img, this.nextSibling);
                    } else {
                        preview.src = e.target.result;
                    }
                }.bind(this);
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const notifToggle = document.getElementById('notifToggle');
    const notifDropdown = document.getElementById('notifDropdown');

    if (notifToggle) {
        notifToggle.addEventListener('click', function(e) {
            e.preventDefault();
            if (notifDropdown.style.display === 'none' || notifDropdown.style.display === '') {
                notifDropdown.style.display = 'block';
            } else {
                notifDropdown.style.display = 'none';
            }
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', function(e) {
            if (!notifToggle.contains(e.target) && !notifDropdown.contains(e.target)) {
                notifDropdown.style.display = 'none';
            }
        });
    }
});

// Admin UI Interactions
document.addEventListener('DOMContentLoaded', () => {
  // Confirm delete forms
  document.querySelectorAll('form.confirm-delete').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const message = form.getAttribute('data-message') || 'Are you sure you want to delete this record? This action cannot be undone.';
      const confirmed = await Notify.confirm('Confirm Deletion', message, 'Yes, Delete');
      if (confirmed.isConfirmed) {
        form.submit();
      }
    });
  });
});

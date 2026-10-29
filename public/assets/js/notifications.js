// Global SweetAlert2 toast & modal helpers
window.Notify = {
  toast: function(message, icon = 'success') {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    } else {
      console.log(`[Notification ${icon}]`, message);
    }
  },
  confirm: function(title, text, confirmButtonText = 'Yes, Proceed') {
    if (typeof Swal !== 'undefined') {
      return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#64748b',
        confirmButtonText: confirmButtonText
      });
    }
    return Promise.resolve({ isConfirmed: window.confirm(`${title}\n${text}`) });
  }
};

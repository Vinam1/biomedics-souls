document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-auto-close');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('fade');
            alert.addEventListener('transitionend', function () {
                alert.remove();
            });
        }, 4000);
    });
});

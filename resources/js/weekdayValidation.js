document.addEventListener('DOMContentLoaded', function() {
    const scheduleDateInput = document.getElementById('schedule_date');
    if (scheduleDateInput) {
        scheduleDateInput.addEventListener('change', function() {
            const date = new Date(this.value);
            const day = date.getDay();
            if (day === 0 || day === 6) { // 0 = Sunday, 6 = Saturday
                alert('Csak hétköznap (hétfő-péntek) választható!');
                this.value = '';
            }
        });
    }
});
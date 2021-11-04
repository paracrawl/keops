$(document).ready(function() {
    /**
     * Evaluation shortcuts
     */
    $(document).on('keydown', (e) => {
        if ($(e.target).is('input[type=text], input[type=number], select, label') && e.which == 13) {
            $(e.target).find(".btn").trigger('click');
        } else if ($(e.target).is('input[type=text], input[type=number], select, label')) {
            return;
        } else {
            if (e.which == 13) { // Intro
                $("#evalutionsavebutton").trigger('click');
            } else {
                if (e.which === 83) {
                    // S
                    window.location.href = $("#skipBtn").attr("href");
                }
            }
        }
    });
});

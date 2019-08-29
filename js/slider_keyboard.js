$(document).ready(function() {
    let slider = initSlider(".custom-range");

    $(document).on('keypress', (e) => {
        if ($(e.target).is('input, select, label, a, .btn')) return
        switch(e.which) {
            case 45:
                slider.back(10)
                break;

            case 46:
                slider.move(50);
                break;

            case 43:
                slider.forward(10);
                break;
        }
    });
});
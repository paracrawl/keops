$(document).ready(function() {
    let slider = initSlider(".custom-range");

    $(document).on('keypress', (e) => {
        if ($(e.target).is('input, select, label, a, .btn')) return
        
        switch(e.which) {
            case 106:
                slider.back(10)
                break;

            case 107:
                slider.move(50);
                break;

            case 108:
                slider.forward(10);
                break;
        }
    });
});
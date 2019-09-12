$(document).ready(function() {
  /**
   * Evaluation shortcuts
   */
  let annotations = $(".btn-annotation");
  $(document).on('keypress', (e) => {
      console.log(e.which);
    if ($(e.target).is('input[type=text], input[type=number], select, label') && e.which == 13) {
      $(e.target).find(".btn").trigger('click');
    } else if ($(e.target).is('input[type=text], input[type=number], select, label')) {
      return;
    } else {
      if (e.which == 13) { // Intro
        $("#evalutionsavebutton").trigger('click');
      } else {
        if ($("#evaluation-container").attr("data-done") == "1") return;

        let active_question = $('.active-question');
        if (e.which == 46 || e.which == 44) {
            let options = $(active_question).children('.btn-group').children('.btn');
            let option = (e.which == 44) ? options.first() : options.last();
            console.log(options, option);
            $(option).trigger('click');
            return;
        }

        if (e.which < 49 || e.which > 56) return;

        $("#evaluationform :radio").removeAttr('checked');
        $("#evaluationform label.active").removeClass("active");
        
        let annotation = annotations[e.which - 49];
        toggleAnnotation(annotation);
      }
    }
  });
});

function toggleAnnotation(e) {
    $('.btn-annotation').removeClass('active');
    $('.btn-annotation input').prop('checked', false);

    $(e).addClass('active');
    $(e).find('input').prop('checked', true);

    $('.question-column').addClass('d-none');
    $('.question-column').removeClass('active-question');
    $('.question-column input').prop('checked', false);

    if ($(e).closest('.row').find('.question-column').children().length > 0) {
        $(e).closest('.row').find('.question-column').removeClass('d-none');
        $(e).closest('.row').find('.question-column').addClass('active-question');
    }

    $(e).focus();
}
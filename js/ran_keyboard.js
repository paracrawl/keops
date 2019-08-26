$(document).ready(function() {

  /**
   * Evaluation shortcuts
   */
  let ranking = $(".ranking input");
  let iterator = 0;
  $(document).on('keypress', (e) => {
      console.log(e.which);
    if ($(e.target).is('input, select, label, a, .btn') && e.which == 13) {
      $(e.target).find(".btn").trigger('click');
    } else if ($(e.target).is('input, select, label')) {
      return;
    } else {
      if (e.which == 13) { // Intro
        $("#evalutionsavebutton").trigger('click');
      } else {
        if ($("#evaluation-container").attr("data-done") == "1") return;

        if (e.which < 49 || e.which > 53) return;

        $("#evaluationform :radio").removeAttr('checked');
        $("#evaluationform label.active").removeClass("active");
        
        let value = e.which - 48;
        for (position of ranking) {
            if ($(position).val() == value) $(position).val("");
        }

        $(ranking[iterator]).val(value);

        iterator = (iterator + 1 == ranking.length) ? 0 : iterator + 1;
      }
    }
  });
})
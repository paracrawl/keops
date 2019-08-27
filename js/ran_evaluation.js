$(document).ready(function() {

  /**
   * Evaluation form
   */
  $("#evaluationform").on('submit', function(e) {
    let ranking = [];
    let done = true;
    $(".ranking-item").each(function(i, el) {
      done = done && ($(el).find(".ranking-position input").val() != "");
      ranking[$(el).attr("data-sentence-id")] = $(el).find(".ranking-position input").val();
    });

    $("input[name='evaluation']").val((done) ? JSON.stringify(ranking) : "P");
    
    return true;
  })


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

        $(".ranking input").css("font-weight", "normal");
        $(ranking[iterator]).val(value);
        $(ranking[iterator]).css("font-weight", "bold");

        iterator = (iterator + 1 == ranking.length) ? 0 : iterator + 1;
      }
    }
  });
})
$(document).ready(function() {

  /**
   * Evaluation form
   */
  $("#evaluationform").on('submit', function(e) {
    let ranking = {};
    let done = true;
    $(".ranking-item").each(function(i, el) {
      done = done && ($(el).find(".ranking-position input").val() != "");
      ranking[$(el).attr("data-sentence-system")] = $(el).find(".ranking-position input").val();
    });
    $("input[name='evaluation']").val((done) ? JSON.stringify(ranking) : "P");

    return true;
  });

  $(".ranking-position input").on('change', function(e) {
    let value = $(this).val();
    if (value < parseInt($(this).attr('min')) || value > parseInt($(this).attr('max'))) {
      $(this).val("");
    } else {
      for (let option of $(".ranking-position input").not(this)) {
        if ($(option).val() == value) {
          $(this).val("");
        }
      }
    }
  });

  /**
   * Evaluation shortcuts
   */
  let ranking = $(".ranking input");
  let iterator = 0;
  $(document).on('keypress', (e) => {
    if ($(e.target).is('input, select, label, a, .btn') && e.which == 13) {
      $(e.target).find(".btn").trigger('click');
    } else if ($(e.target).is('input, select, label')) {
      return;
    } else {
      if (e.which == 13) { // Intro
        $("#evalutionsavebutton").trigger('click');
      } else {
        if ($("#evaluation-container").attr("data-done") == "1") return;

        if (e.which == 45) { // -
          iterator = (iterator - 1 < 0) ? ranking.length - 1 : iterator - 1;
          $(".ranking input").css("border-color", "#ccc");
          $(ranking[iterator]).css("border-color", "#66afe9");
          return;
        }

        if (e.which == 43) { // +
          iterator = (iterator + 1 == ranking.length) ? 0 : iterator + 1;
          $(".ranking input").css("border-color", "#ccc");
          $(ranking[iterator]).css("border-color", "#66afe9");
          return;
        }

        if (e.which == 127) { // supr
          $(ranking[iterator]).val("");
          return;
        }

        if (e.which == 46) { // .
          iterator = 0;
          $(".ranking input").css("border-color", "#ccc");
          $(ranking[iterator]).css("border-color", "#66afe9");
          return;
        }

        if (e.which < 49 || e.which > 53) return;

        $("#evaluationform :radio").removeAttr('checked');
        $("#evaluationform label.active").removeClass("active");
        
        let value = e.which - 48;
        let jump;
        ranking.each(function (i, position) {
          if ($(position).val() == value) {
            $(position).val("");
            jump = (i != iterator) ? i : undefined;
          }
        });

        $(ranking[iterator]).val(value);

        iterator = (jump != undefined) ? jump : (iterator + 1 == ranking.length) ? 0 : iterator + 1;

        $(".ranking input").css("border-color", "#ccc");
        $(ranking[iterator]).css("border-color", "#66afe9");
      }
    }
  });

  let clickcount = 0;
  $(".ranking-text").on('click', function() {
    if ($("#evaluation-container").attr("data-done") == "1") return;

    ranking.each(function (i, position) {
      if ($(position).val() == (clickcount + 1)) {
        $(position).val("");
      }
    });

    $(this).siblings('.ranking-position').find('input').val(clickcount + 1);

    clickcount = (clickcount + 1 == ranking.length) ? 0 : clickcount + 1;
    iterator = clickcount;
  });
})
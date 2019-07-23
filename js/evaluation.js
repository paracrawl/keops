$(".evaluation_tab_link").on('click', function (e) {
  e.preventDefault();

  if ($(this).hasClass("disabled")) {
    e.stopPropagation();
    return;
  };
  
  $('.evaluation-tab-content .tab-pane').removeClass("active");
  $('.evaluation-tab-content input[type="radio"]').removeAttr("checked");

  if ($($(this).attr('href')).length > 0) {
    $(this).tab('show');
  }
});

$('.evaluation_tab_link .btn.active').parent().tab('show');

$('#evalutionsavebutton').on('click', function() {
  if ($(this).attr("data-next")) {
    window.location.href = $(this).attr("data-next");
  } else {
    $('#evaluationform').submit();
  }
});

/**
 * Evaluation shortcuts
 */

$(document).on('keypress', (e) => {
  if ($(e.target).is('input, select, label, a, .btn') && e.which == 13) {
    $(e.target).find(".btn").trigger('click');
  } else {
    if (e.which == 13) { // Intro
      $("#evalutionsavebutton").trigger('click');
    } else {
      if ($("#evaluation-container").attr("data-done") == "1") return;

      $("#evaluationform :radio").removeAttr('checked');
      $("#evaluationform label.active").removeClass("active");

      if (e.which == 76 || e.which == 108) {//L
        $("#evaluationform label input[value='L']").parent().trigger('click');
      }

      if (e.which == 65 || e.which == 97) {//A
        $("#evaluationform label input[value='A']").parent().trigger('click');
      }

      if (e.which == 84 || e.which == 116) {//T
        $("#evaluationform label input[value='T']").parent().trigger('click');
      }

      if (e.which == 77 || e.which == 109) {//MT / M
        $("#evaluationform label input[value='MT']").parent().trigger('click');
      }

      if (e.which == 69 || e.which == 101) {//E
        $("#evaluationform label input[value='E']").parent().trigger('click');
      }

      if (e.which == 70 || e.which == 102) {//F
        $("#evaluationform label input[value='F']").parent().trigger('click');
      }

      if (e.which == 86 || e.which == 118) {//V
        $("#evaluationform label input[value='V']").parent().trigger('click');
      };
      

      if (e.which == 80 || e.which == 112) {//P
        $("#evaluationform label input[value='P']").parent().trigger('click');
      }
    }
  }
});

/*
$(document).keypress(function (e) {
//  console.log(e.which);
//  console.log(e.keyCode);
  if (document.activeElement.id != "comments" && document.activeElement.id != "gotopage" && document.activeElement.id != "search-term") {
    
    if (e.which == 13) {//INTRO
      $("#evalution-save-button").click();
    } else {
      $("#evaluation-form :radio").removeAttr('checked');
//  $("#evalution-form:radio").buttonset('refresh');

      if (e.which == 76 || e.which == 108) {//L
        $("#evaluation-form :radio[value=L]").attr("checked", "true");
      }

      if (e.which == 65 || e.which == 97) {//A
        $("#evaluation-form :radio[value=A]").attr("checked", "true");
      }

      if (e.which == 84 || e.which == 116) {//T
        $("#evaluation-form :radio[value=T]").attr("checked", "true");
      }

      if (e.which == 77 || e.which == 109) {//MT / M
        $("#evaluation-form :radio[value=MT]").attr("checked", "true");
      }

      if (e.which == 69 || e.which == 101) {//E
        $("#evaluation-form :radio[value=E]").attr("checked", "true");
      }

      if (e.which == 70 || e.which == 102) {//F
        $("#evaluation-form :radio[value=F]").attr("checked", "true");
      }

      if (e.which == 86 || e.which == 118) {//V
        $("#evaluation-form :radio[value=V]").attr("checked", "true");
      };
      

      if (e.which == 80 || e.which == 112) {//P
        $("#evaluation-form :radio[value=P]").attr("checked", "true");
      }
    }
  }
});*/
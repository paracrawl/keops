$(".evaluation_tab_link").on('click', function (e) {
  e.preventDefault();
  
  $('.evaluation-tab-content .tab-pane').removeClass("active");
  $('.evaluation-tab-content input[type="radio"]').removeAttr("checked");

  if ($($(this).attr('href')).length > 0) {
    $(this).tab('show');
  }
});

$('.evaluation_tab_link .btn.active').parent().tab('show');

$('#evalutionsavebutton').on('click', () => {
  $('#evaluationform').submit();
})

/**
 * Evaluation shortcuts
 */

$(document).on('keypress', (e) => {
  if ($(e.target).is('input')) return;

  if (e.which == 13) { // Intro
    $("#evalutionsavebutton").trigger('click');
  } else {
    $("#evaluationform :radio").removeAttr('checked');
    $("#evaluationform label.active").removeClass("active");

    if (e.which == 76 || e.which == 108) {//L
      $("#evaluationform :radio[value=L]").attr("checked", "true");
      $("#evaluationform label input[value='L']").parent().addClass("active");
    }

    if (e.which == 65 || e.which == 97) {//A
      $("#evaluationform :radio[value=A]").attr("checked", "true");
      $("#evaluationform label input[value='A']").parent().addClass("active");
    }

    if (e.which == 84 || e.which == 116) {//T
      $("#evaluationform :radio[value=T]").attr("checked", "true");
      $("#evaluationform label input[value='T']").parent().addClass("active");
    }

    if (e.which == 77 || e.which == 109) {//MT / M
      $("#evaluationform :radio[value=MT]").attr("checked", "true");
      $("#evaluationform label input[value='MT']").parent().addClass("active");
    }

    if (e.which == 69 || e.which == 101) {//E
      $("#evaluationform :radio[value=E]").attr("checked", "true");
      $("#evaluationform label input[value='E']").parent().addClass("active");
    }

    if (e.which == 70 || e.which == 102) {//F
      $("#evaluationform :radio[value=F]").attr("checked", "true");
      $("#evaluationform label input[value='F']").parent().addClass("active");
    }

    if (e.which == 86 || e.which == 118) {//V
      $("#evaluationform :radio[value=V]").attr("checked", "true");
      $("#evaluationform label input[value='V']").parent().addClass("active");
    };
    

    if (e.which == 80 || e.which == 112) {//P
      $("#evaluationform :radio[value=P]").attr("checked", "true");
      $("#evaluationform label input[value='P']").parent().addClass("active");
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
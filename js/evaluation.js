$(document).ready(function() {

  $('.btn-annotation.active').closest('.row').find('.question-column').removeClass('d-none');

  $(".btn-annotation").on('click', function (e) {
    e.preventDefault();

    if ($(this).hasClass("disabled")) {
      e.stopPropagation();
      return;
    };
    
    // Make active
    $('.btn-annotation').removeClass('active');
    $('.btn-annotation input').prop('checked', false);

    $(this).addClass('active');
    $(this).find('input').prop('checked', true);
    
    // Show annotation question
    $('.question-column').addClass('d-none');
    $('.question-column input').prop('checked', false);
    $(this).closest('.row').find('.question-column').removeClass('d-none');
  });

  $("#evaluationform .btn-group .btn").on('click', function (e) {
    if ($(this).hasClass("disabled")) {
      e.stopPropagation();
      return;
    }
  });

  $('.question-column .btn-group .btn').on('click', function(e) {
    if ($(this).parent().attr('data-single') == '1') {
      $(this).parent().find('.btn').removeClass('active');
      $(this).parent().find('.btn input').prop('checked', false);
    }

    if ($(this).hasClass('active')) {
      setTimeout(function() {
        $(this).removeClass('active').find('input').prop('checked', false);
      }.bind(this), 10);
    }
  });

  $('#evalutionsavebutton').on('click', function() {
    if ($(this).attr("data-next")) {
      window.location.href = $(this).attr("data-next");
    } else {
      $('#evaluationform').submit();
    }
  });

  $(".search-form").on('submit', function(e) {
    if ($("#search-term").val() == "" && $("select[name='label'] option:selected").val() == "ALL") {
      e.preventDefault();
      window.location.href = $("input[name='seall']").val();
    }
  });

  $("select[name='label']").on('change', function() {
    $(".search-form").submit();
  });

  $('.current-page-control').on('change', function() {
    $('.current-page-control').on('blur', function() {
      console.log('blur');
      $(this).closest('form').submit();
    })
  });

  /**
   * Evaluation shortcuts
   */

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
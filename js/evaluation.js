$(document).ready(function() {
  let timer = timerjs.build();
  timer.start();

  $('.btn-annotation.active').closest('.row').find('.question-column').removeClass('d-none');

  $(".btn-annotation").on('click', function (e) {
    e.preventDefault();

    if ($(this).hasClass("disabled")) {
      e.stopPropagation();
      return;
    };

    if ($(this).hasClass('active')) {
      setTimeout(function() {
        $(this).removeClass('active').removeClass('focus').find('input').prop('checked', false);
        
        if ($(this).closest('.row').find('.question-column').children().length > 0) {
          $(this).closest('.row').find('.question-column').addClass('d-none');
        }
      }.bind(this), 10);
    } else {
      // Make active and show question
      toggleAnnotation(this);
    }
    
  });

  $("#evaluationform .btn-group .btn").on('click', function (e) {
    if ($(this).hasClass("disabled")) {
      e.stopPropagation();
      return;
    }
  });

  $('.question-column .btn-group .btn').on('click', function(e) {
    if ($(this).hasClass('active')) {
      setTimeout(function() {
        $(this).removeClass('active').removeClass('focus').find('input').prop('checked', false);
      }.bind(this), 10);
    }

    if ($(this).parent().attr('data-single') == '1') {
      $(this).parent().find('.btn').removeClass('active');
      $(this).parent().find('.btn input').prop('checked', false);
    }
  });

  $('#evalutionsavebutton').on('click', function() {
    timer.stop();
    console.log('Time', timer.get());
    if ($(this).attr("data-next")) {
      window.location.href = $(this).attr("data-next");
    } else {
      let time_el = document.createElement('input');
      $(time_el).attr('name', 'time');
      $(time_el).attr('type', 'hidden');
      $(time_el).val(timer.get());
      $('#evaluationform').append(time_el);

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
  let annotations = $(".btn-annotation");
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
  $('.question-column input').prop('checked', false);

  if ($(e).closest('.row').find('.question-column').children().length > 0) {
    $(e).closest('.row').find('.question-column').removeClass('d-none');
  }

  $(e).focus();
}
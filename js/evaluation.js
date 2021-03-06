$(document).ready(function() {
  $('.btn-annotation.active').closest('.row').find('.question-column').removeClass('d-none');
  $('.btn-annotation.active').closest('.row').find('.question-column').addClass('active-question')
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

  $(document).on('keypress', (e) => {
    if (e.which == 13) { // Intro
      $("#evalutionsavebutton").trigger('click');
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
});
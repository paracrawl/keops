$(document).ready(function () {
  /**
   * Evaluation shortcuts
   */

  let numberInput = $("input[name=id]");
  let correctLanguageInput = $("input[value=CL]");
  let wrongLanguageInput = $("input[value=WL]");
  let mixedLanguageInput = $("input[value=ML]");

  let secondLoop = $(".second-loop");
  let thirdLoop = $(".third-loop");

  correctLanguageInput.parent("label").on("click", () => {
    if (!correctLanguageInput.is(":checked")) {
      secondLoop.addClass("d-none");
    } else if (correctLanguageInput.is(":checked")) {
      secondLoop.removeClass("d-none");
      secondLoop.css("display", "flex");
    }
    if (!thirdLoop.hasClass("d-none")) {
      thirdLoop.toggleClass("d-none");
    }
  });

  wrongLanguageInput.parent("label").on("click", () => {
    if (!secondLoop.hasClass("d-none")) {
      secondLoop.toggleClass("d-none");
      secondLoop.css("display", "none");
    }
    if (!thirdLoop.hasClass("d-none")) {
      thirdLoop.toggleClass("d-none");
      thirdLoop.css("display", "none");
    }
  });

  mixedLanguageInput.parent("label").on("click", () => {
    if (!secondLoop.hasClass("d-none")) {
      secondLoop.toggleClass("d-none");
      secondLoop.css("display", "none");
    }
    if (!thirdLoop.hasClass("d-none")) {
      thirdLoop.toggleClass("d-none");
      thirdLoop.css("display", "none");
    }
  });

  ///

  let missingContentInput = $("input[value=MC");
  let replacedContentInput = $("input[value=RC");
  let misalignmentInput = $("input[value=MA");
  let sameContentInput = $("input[value=SC]");
  let lowQualityTranslationInput = $("input[value=LQT]");
  let correctBoilerplateTranslationInput = $("input[value=CBT]");
  let resonableTranslationInput = $("input[value=RT]");

  if (
    missingContentInput.parent("label").hasClass("active") ||
    replacedContentInput.parent("label").hasClass("active") ||
    misalignmentInput.parent("label").hasClass("active")
  ) {
    secondLoop.removeClass("d-none");
    secondLoop.css("display", "flex");
  }

  if (
    lowQualityTranslationInput.parent("label").hasClass("active") ||
    correctBoilerplateTranslationInput.parent("label").hasClass("active") ||
    resonableTranslationInput.parent("label").hasClass("active")
  ) {
    secondLoop.removeClass("d-none");
    secondLoop.css("display", "flex");
    thirdLoop.removeClass("d-none");
    thirdLoop.css("display", "flex");
  }

  sameContentInput.parent("label").on("click", () => {
    if (!sameContentInput.is(":checked")) {
      thirdLoop.addClass("d-none");
    } else if (sameContentInput.is(":checked")) {
      thirdLoop.removeClass("d-none");
      thirdLoop.css("display", "flex");
    }
  });

  replacedContentInput.parent("label").on("click", () => {
    if (!thirdLoop.hasClass("d-none")) {
      thirdLoop.toggleClass("d-none");
      thirdLoop.css("display", "none");
    }
  });
  misalignmentInput.parent("label").on("click", () => {
    if (!thirdLoop.hasClass("d-none")) {
      thirdLoop.toggleClass("d-none");
      thirdLoop.css("display", "none");
    }
  });
  missingContentInput.parent("label").on("click", () => {
    if (!thirdLoop.hasClass("d-none")) {
      thirdLoop.toggleClass("d-none");
      thirdLoop.css("display", "none");
    }
  });

  let annotations = $(".btn-annotation");
  $(document).on("keydown", (e) => {
    if (!numberInput.is(":focus")) {
      if (e.which == 49 || e.which == 97) {
        wrongLanguageInput.trigger("click");
      }
      if (e.which == 50 || e.which == 98) {
        mixedLanguageInput.trigger("click");
      }
      if (e.which == 51 || e.which == 99) {
        correctLanguageInput.trigger("click");
      }
      if (e.which == 52 || e.which == 100) {
        missingContentInput.trigger("click");
      }
      if (e.which == 53 || e.which == 101) {
        replacedContentInput.trigger("click");
      }

      if (e.which == 54 || e.which == 102) {
        misalignmentInput.trigger("click");
      }
      if ((e.which == 55 || e.which == 103) && !secondLoop.hasClass("d-none")) {
        sameContentInput.trigger("click");
      }
      ////
      if (e.which == 56 || e.which == 104) {
        lowQualityTranslationInput.trigger("click");
      }

      if (e.which == 57 || e.which == 105) {
        correctBoilerplateTranslationInput.trigger("click");
      }
      if (e.which == 86) {
        resonableTranslationInput.trigger("click");
      }
      //////
      if (
        $(e.target).is("input[type=text], input[type=number], select, label") &&
        e.which == 13
      ) {
        $(e.target).find(".btn").trigger("click");
      } else if (
        $(e.target).is("input[type=text], input[type=number], select, label")
      ) {
        return;
      } else {
        if (e.which == 13) {
          // Intro
          $("#evalutionsavebutton").trigger("click");
        } else {
          if ($("#evaluation-container").attr("data-done") == "1") return;

          let active_question = $(".active-question");
          if (e.which == 37 || e.which == 39) {
            let options = $(active_question)
              .children(".btn-group")
              .children(".btn");
            let option = e.which == 37 ? options.first() : options.last();
            console.log(options, option);
            $(option).trigger("click");
            return;
          }

          // Transform numpad keys

          if (e.which >= 97 && e.which <= 104) e.which -= 48;

          if (e.which < 47 || e.which > 57) return;

          $("#evaluationform :radio").removeAttr("checked");
          $("#evaluationform label.active").removeClass("active");

          let annotation = annotations[e.which - 49];
          toggleAnnotation(annotation);
        }
      }
    }
  });
});

function toggleAnnotation(e) {
  $(".btn-annotation").removeClass("active");
  $(".btn-annotation input").prop("checked", false);

  $(e).addClass("active");
  $(e).find("input").prop("checked", true);

  $(".question-column").addClass("d-none");
  $(".question-column").removeClass("active-question");
  $(".question-column input").prop("checked", false);

  if ($(e).closest(".row").find(".question-column").children().length > 0) {
    $(e).closest(".row").find(".question-column").removeClass("d-none");
    $(e).closest(".row").find(".question-column").addClass("active-question");
  }

  $(e).focus();
}


$(document).keypress(function (e) {
//  console.log(e.which);
//  console.log(e.keyCode);
  console.log("HOLI");
  if (document.activeElement.id != "comments") {
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
      }

      if (e.which == 80 || e.which == 112) {//P
        $("#evaluation-form :radio[value=P]").attr("checked", "true");
      }
    }
  }
});

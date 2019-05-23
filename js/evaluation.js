/**
 * Evaluation shortcuts
 */
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
});
/**
 * Search button, to search a term in the task's sentences
 */
$('body').on('click', "#search-term-button", function (e) {
  e.preventDefault();
  var search_term = $("#search-term").val();
  var task_id = $("#task_id").val();
  if (search_term.length > 0 && task_id>0) {
    $.ajax({
      data: {"search_term": search_term, "task_id": task_id},
      url: 'search_sentence.php',
      type: 'post',
      dataType: 'json',
      success: function (response) {
        if (response  == 0) {
          alert ("Search term not found");
        }
        else {
          $("#gotopage").val(response);
          $("#gotoform").submit();
        }
      },
      error: function (response) {
        alert("Sorry, your request could not be processed. Please, try again later. ");
      }


    });
  }
});
$(document).ready(function() {
    $("#feedbackBtn").on('click', function() {
        $.ajax({
            url: "/services/feedback_service.php?service=post",
            data: {
                feedback_score: $("input[name='feedback_score']:checked").val(),
                feedback_comments: $("textarea[name='feedback_comments']").val(),
                feedback_task_id: $("input[name='feedback_task_id']").val()
            },
            type: "POST",
            success: function(json) {
                let response = JSON.parse(json);
                if (response.result == 200) {
                $("#feedback-success-label").removeClass("d-none");
                $("#feedback-success-label").addClass("d-block");
                }
            }
        })
    });
});
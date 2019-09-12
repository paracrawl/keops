<div class="feedback m-sm-4">
    <div class="h4 text-center">
        Are you satisfied with KEOPS?
    </div>

    <div class="row">
        <div class="col-sm-6 col-sm-offset-3 col-xs-12">
            <div class="feedback-area">
                <div class="btn-group-vertical w-100 mb-3" data-toggle="buttons">
                    <label class="btn btn-default text-left">
                        <input type="radio" name="feedback_score" value="3" required> <img src="/img/feedback_incredible.png" alt="Awesome" class="mr-3" /> It's awesome!
                    </label>
                    <label class="btn btn-default text-left">
                        <input type="radio" name="feedback_score" value="2" required> <img src="/img/feedback_happy.png" alt="OK" class="mr-3" /> It's OK
                    </label>
                    <label class="btn btn-default text-left">
                        <input type="radio" name="feedback_score" value="1" required> <img src="/img/feedback_sad.png" alt="Not good" class="mr-3" /> It's not good, sorry
                    </label>
                </div>

                If you wish, tell us more
                <textarea name="feedback_comments" class="form-control w-100" maxlength="240" placeholder="Max. 240 characters"></textarea>

                <input type=hidden name="feedback_task_id" value="<?= $task->id ?>" />
                <button id="feedbackBtn" class="btn btn-primary w-100 mt-3">Send</button>
            </div>
            <div id="feedback-success-label" class="label label-success w-100 mt-3 p-2 d-none">
                <span class="glyphicon glyphicon-ok"></span> Thanks for your feedback!
            </div>
        </div>
    </div>
</div>
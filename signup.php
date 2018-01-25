<?php    
  // load up your config file
  require_once($_SERVER['DOCUMENT_ROOT'] ."/resources/config.php");

  require_once(TEMPLATES_PATH . "/header.php");
  
  session_start();

?>
    <div class="container">
      <div class="page-header">
        <h1>Sign up</h1>
        <p>Please enter the required fields to create your account.</p>

        <div class="panel panel-primary" style="margin-top: 20px;">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Note</strong></h3>
          </div>
          <div class="panel-body">
            <p>If you are here, it means that you were invited to participate in some of the evaluation tasks opened in this system. If you don't have an invitation, you won't be able to register. You will be redirected to the evaluation tasks if you are successfully registered.</p>
          </div>
        </div>
      </div>
      <form class="form-horizontal" method="post" action="">
        <div class="form-group">
          <label for="username" class="col-sm-4 control-label">Username</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="username" id="username" type="text" size="30" value="" aria-describedby="helpUsername">
            </div>
            <span id="helpUsername" class="help-block">Please enter your desired username</span>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-4 control-label">Email</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <input class="form-control" name="email" id="email" type="text" size="30" value="" aria-describedby="helpEmail">
            </div>
            <span id="helpEmail" class="help-block">Please enter your email address</span>
          </div>
        </div>
        <div class="form-group">
          <label for="id_projects" class="col-sm-4 control-label">Projects</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <select class="form-control" name="projects" id="id_projects" multiple="multiple">

                <option value="NewsTask">NewsTask</option>

                <option value="ITTask">ITTask</option>

                <option value="Testing">Testing</option>

              </select>
            </div>
            <span id="helpProjects" class="help-block">Which annotation projects will you work on?</span>
          </div>
        </div>

        <div class="form-group">
          <label for="id_languages" class="col-sm-4 control-label">Languages</label>
          <div class="col-sm-4">
            <div class="input-group">
              <div class="input-group-addon">required</div>
              <select class="form-control" name="languages" id="id_languages" multiple="multiple">
                <option value="2baq">Basque (Euskara)</option>
                <option value="2bul">Bulgarian (Български)</option>
                <option value="2ces">Czech (Čeština)</option>
                <option value="2deu">German (Deutsch)</option>
                <option value="2eng">English</option>
                <option value="2fin">Finnish (Suomi)</option>
                <option value="2nld">Dutch (Nederlands)</option>
                <option value="2ptb">Portuguese (Português)</option>
                <option value="2rom">Romanian (Română)</option>
                <option value="2rus">Russian (Русский)</option>
                <option value="2esn">Spanish (Español)</option>
                <option value="2trk">Turkish (Türkçe)</option>
              </select>
            </div>
            <span id="helpLanguages" class="help-block">Which target languages will you evaluate?</span>
          </div>
        </div>

        <button type="submit" class="col-sm-offset-4 btn btn-primary">Sign up</button>

      </form>
    </div>
<?php
  require_once(TEMPLATES_PATH . "/footer.php");
?>

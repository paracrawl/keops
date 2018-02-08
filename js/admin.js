// Variable for corpora table used in dropzone
var corpora_table = null;

// Dropzone
Dropzone.options.dropzone = { // camelized id
  paramName: "file",
  maxFilesize: 10, // 10 MB
  filesizeBase: 1000,
  //acceptedFiles: "text/*,application/*",
  //maxFiles: 40, // needed?
  init: function() {
    this.on("complete", function(file) {
      obj = this;
      setTimeout(function() {
        obj.removeFile(file);
        corpora_table.ajax.reload();
      }, 10000);
      corpora_table.ajax.reload();
    });

    this.on("canceled", function(file) {
      this.removeFile(file);
    });
  },
  sending: function(file, xhr, formData) {
    formData.append("source_lang", $("#source_lang"));
    formData.append("target_lang", $("#target_lang"));
  },
  autoProcessQueue: true
};

$(document).ready(function() {
  var users_table = $("#users-table").DataTable({
    columnDefs: [ {
      targets: 1,
      data: function( row, type, val, meta ) {
        return '<a href="/admin/user_edit.php?id=' + row[0] + '">' + row[1] + '</a>';
      }
    },
    {
      targets: 5,
      className: "text-center",
      data: function ( row, type, val, meta ) {
        if (row[5]){
          return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
      }
    }],
    order: [[ 1, 'asc' ]],
    processing: true,
    serverSide: true,
    ajax: "/users/user_list.php",
    stateSave: true
  });
  
  var projects_table = $("#projects-table").DataTable({
    columnDefs: [ {
      targets: 1,
      data: function( row, type, val, meta ) {
        return '<a href="/projects/project_manage.php?id=' + row[0] + '">' + row[1] + '</a>';
      }
    },
    {
      targets: 7,
      className: "text-center",
      data: function ( row, type, val, meta ) {
        if (row[7]){
          return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
      }
    },
    {
      targets: 8,
      className: "actions",
      data: function ( row, type, val, meta ) {
        return '<a href="/projects/project_manage.php?id=' + row[0] + '" title="Manage project\'s tasks"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span></a>' +
                '<a href="/projects/project_edit.php?id=' + row[0] + '" title="Edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>' +
                '<a href="/projects/project_stats.php?id=' + row[0] + '" title="View stats"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span></a>'
      }
    }],
    order: [[ 6, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: "/services/project_service.php?service=list_dt",
    stateSave: true
  });
  
  var languages_table = $("#languages-table").DataTable({
    pageLength: 25,
    order: [[ 1, 'asc' ]],
    processing: true,
    serverSide: true,
    ajax: "/languages/language_list.php",
    stateSave: true
  });
  
  var invitations_table = $("#invitations-table").DataTable({
      columnDefs: [{
        targets: 6,
        className: "text-center",
        data: function (row, type, val, meta) {
          if (row[3] == "") {
            return '<a href="/admin/revoke_invite.php?id=' + row[0] + '"><span class="glyphicon glyphicon-remove red" aria-hidden="true"></span></a>';
            //return "HOLA";
          }
          else {
            return ""; 
         }
        }
      }],
    order: [[2, 'desc']],
    processing: true,
    serverSide: true,
    ajax: "/admin/invite_list.php",
    stateSave: true
  });
  
  corpora_table = $("#corpora-table").DataTable({
    columnDefs: [ /*{
      targets: 1,
      data: function( row, type, val, meta ) {
        return '<a href="/projects/project_edit.php?id=' + row[0] + '">' + row[1] + '</a>';
      }
    },*/
    {
      targets: 6,
      className: "text-center",
      data: function ( row, type, val, meta ) {
        if (row[6]){
          return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
      }
    }],
    order: [[ 5, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: "/corpora/corpus_list.php",
    stateSave: true
  });
  
  var tasks_table = $("#tasks-table").DataTable({
    /*columnDefs: [{
      targets: 6,
      className: "text-center",
      data: function ( row, type, val, meta ) {
        if (row[6]){
          return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
      }
    }],*/
    order: [[ 4, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: "/tasks/task_list.php",
    stateSave: true
  });
  
  // Activate Bootstrap tab on loading or user click.
  if (location.hash) {
    $('a[href=\'' + location.hash + '\']').tab('show');
  }
  var activeTab = localStorage.getItem('activeTab');
  if (activeTab) {
    $('a[href="' + activeTab + '"]').tab('show');
  }

  $('body').on('click', 'a[data-toggle=\'tab\']', function (e) {
    e.preventDefault();
    var tab_name = this.getAttribute('href');
    if (history.pushState) {
      history.pushState(null, null, tab_name);
    }
    else {
      location.hash = tab_name;
    }
    localStorage.setItem('activeTab', tab_name);

    $(this).tab('show');
    return false;
  });
  $(window).on('popstate', function () {
    var anchor = location.hash || $('a[data-toggle=\'tab\']').first().attr('href');
    $('a[href=\'' + anchor + '\']').tab('show');
  });
  
  $(document).on('click', '#invite_button', function(event){
    event.preventDefault();
    event.stopPropagation();
    getInviteToken($('#email').val());
  });
});


function getInviteToken(email){
 
  $.ajax({
    data: {"email": email},
    url: 'get_token.php', 
    type: 'post',
    success: function (response) {
      $("#token").val(response);
    },
    error: function(response){
      $("#token").val("Sorry, your request could not be processed. Please, try again later. ");
    }
            
  });
  
}

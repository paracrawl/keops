// Variable for corpora table used in dropzone
var corpora_table = null;

// Dropzone
//Dropzone.autoDiscover = false;

/*
 * Corpora uploader
 */
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
//  previewTemplate: document.querySelector('#custom-dz-template').innerHTML,
  autoProcessQueue: true
};

$(document).ready(function() {
  /*
   * Users table (for "Users" tab)
   */
  var users_table = $("#users-table").DataTable({
    columnDefs: [ {
      targets: 1,
      //data: function( row, type, val, meta ) {
      render: function (data, type, row) {
        return '<a href="/admin/user_edit.php?id=' + row[0] + '" title="Edit user">' + row[1] + '</a>';
        //return row[1]
      },
      searchable: true
    },
    {
      targets: 5,
      className: "text-center",
           render: function (data, type, row) {
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
  
   /*
   * Projects table (for "Projects" tab)
   */
  var projects_table = $("#projects-table").DataTable({
    columnDefs: [ {
      targets: 1,
     render: function (data, type, row) {
        return '<a href="/projects/project_manage.php?id=' + row[0] + '" title="Manage project">' + row[1] + '</a>';
      }
    },
    {
      targets:5,
     render: function (data, type, row) {
       var completed = (parseInt(row[12])/parseInt(row[11])) * 100;
        
        return '<a href="/projects/project_stats.php?id=' + row[0] + '" title="View stats">'+
          '<div title="'+row[12]+' of '+row[11]+' tasks completed"'+'class="progress">' +
          '<div class="progress-bar" role="progressbar" aria-valuenow="' + completed +'"' +
          'aria-valuemin="0" aria-valuemax="100" style="width:' + completed +'%">' +
          '<span>'+row[12] +' of '+ row[11]+'</span></div>' +
          '</div></a>';
       }  
    },
    {
      targets:6,
     render: function (data, type, row) {
       return row[5];
       }
    },
    {
      targets:7,
     render: function (data, type, row) {
       return row [6];
       }
    },
   {
      targets: 8,
      className: "text-center",
      render: function (data, type, row) {
        if (row[7]){
          return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
      }
    },
    {
      targets: 9,
      className: "actions",
      sortable: false,
      orderable:false,
     render: function (data, type, row) {
        return '<a href="/projects/project_manage.php?id=' + row[0] + '" title="Manage project\'s tasks"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span></a>' +
                '<a href="/projects/project_edit.php?id=' + row[0] + '" title="Edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>' +
                '<a href="/projects/project_stats.php?id=' + row[0] + '" title="View stats"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span></a>' +
                '<a href="/projects/project_remove.php?id=' + row[0] + '" title="Remove project"><span class="glyphicon glyphicon-trash" aria-hidden="true"></a>';

      }
    }],
    order: [[ 6, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: "/services/project_service.php?service=list_dt",
    stateSave: true
  });
  
  
   /*
   * Languages table (for "Languages" tab)
   */
  var languages_table = $("#languages-table").DataTable({
    pageLength: 25,
    order: [[ 1, 'asc' ]],
    processing: true,
    serverSide: true,
    ajax: "/languages/language_list.php",
    stateSave: true,
    columnDefs: [{
      targets: 3,
      className: "actions",
             render: function (data, type, row) {
          return  '<a href="/languages/language_edit.php?id=' + row[0] + '" title="Edit language"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';
        },
        sortable: false,
      orderable:false
      }
    ]
  });
  
  
   /*
   * Invitations table (for "Invitations" tab)
   */ 
  var invitations_table = $("#invitations-table").DataTable({
      columnDefs: [{          
        targets: 1,
        className: "email"
      },
      {
        targets: 5,
        data: function (row, type, val, meta) {
          return '<a href="/admin/user_edit.php?id=' + row[6] + '" title="Edit user">' + row[5] + '</a>';
        }
      },
      {
        targets: 6,
        className: "actions",
        sortable: false,
        orderable: false,
      render: function (data, type, row) {
          str = "";
          if (row[3] == "") {
            str = '<a href="/admin/revoke_invite.php?id=' + row[0] + '"><span class="glyphicon glyphicon-remove red" aria-hidden="true" title=\"Revoke invitation\"></span></a>';
            str += '<a class="invitation-link" data-toggle="modal" data-target="#invite_token_modal"><span class="glyphicon glyphicon-link" aria-hidden="true" title=\"Get invitation link\"></span></a>';
          }
          else {
            str = "<span class=\"glyphicon glyphicon-remove disabled\" aria-hidden=\"true\" title=\"This user has already accepted the invitation\"></span>"; 
            str += '<span class="glyphicon glyphicon-link disabled" aria-hidden="true" title=\"This user has already accepted the invitation\"></span>';

         }
        return str;
        }
      }],
    order: [[2, 'desc']],
    processing: true,
    serverSide: true,
    ajax: "/admin/invite_list.php",
    stateSave: true
  });
  
   /*
   * Corpora table (for "Corpora" tab)
   */
   corpora_table = $("#corpora-table").DataTable({
    columnDefs: [{
        targets: 1,
        render: function (data, type, row) {
          return '<a href="/corpora/corpus_preview.php?id=' + row[0] + '" title="Preview corpus">' + row[1] + '</a>';
        }
      },
      {
        targets: 6,
        className: "text-center",
        render: function (data, type, row) {
          if (row[6]) {
            return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
          } else {
            return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
          }
        }
      },
      {
      targets: 7,
      className: "actions",
      render: function (data, type, row) {
        return '<a href="/corpora/corpus_edit.php?id=' + row[0] + '" title="Edit corpus">' + '<span class="glyphicon glyphicon-edit "  aria-hidden=\"true\"></a>'+
               '<a href="/corpora/corpus_remove.php?id=' + row[0] + '" title="Remove corpus">' +  '<span class="glyphicon glyphicon-trash " aria-hidden=\"true\"></a>';
      }
    }
    ],
    order: [[ 5, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: "/corpora/corpus_list.php",
    stateSave: true
  });
  
   /*
   * Tasks table, filtered by project (for "Project tasks" page)
   */
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
    columnDefs:[
      {
        targets: 3,
        render: function(data, type, row) {
          return '<a href="/corpora/corpus_preview.php?id='+row[12]+'" title="Preview corpus">'+row[3]+'</a>';
        }
      },
      {
        targets:4,
      render: function (data, type, row) {
//        if (row[2]==null  || row[3] == "DONE") return  '<a href="/tasks/recap.php?id=' + row[0] + '">'+row[3]+'</a>';
//        if (row[2]==null  || row[3] == "PENDING")  return  '<a href="/tasks/recap.php?id=' + row[0] + '">'+row[3]+'</a>';        
        var completed = (parseInt(row[10])/parseInt(row[2])) * 100;        
        return '<a href="/tasks/recap.php?id=' + row[0] + '"><div title="'+row[10]+' of '+row[2]+' sentences evaluated" class="progress">' +
          '<div   class="progress-bar" role="progressbar" aria-valuenow="' + completed +'"' +
          'aria-valuemin="0" aria-valuemax="100" style="width:' + completed +'%">' +
          '<span>'+row[10] +' of '+ row[2]+'</span></div>' +
          '</div></a>';
      }
    },
      {
        targets: 8,
        className: "actions",
        sortable: false,
        searchable: false,
     render: function (data, type, row) {
          var actions_str = "";
          actions_str += '<a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span></a>';
          actions_str += '<a href="mailto:' + row[11] + '" title="Contact assigned user"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>';
          
          if (document.getElementById("input_isowner") && document.getElementById("input_isowner").value == "1") {
            actions_str += '<a href="/tasks/change_assigned_user.php?task_id=' + row[0] + '" title="Change assigned user"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>';
          }
          actions_str += getRemoveTaskCode(row[0], row[1]);
          
          return actions_str;
          
        }
      }
    ],
    order: [[ 6, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: {
        url: "/tasks/task_list.php",
        data: function (d) {
          d.p_id = $("#tasks-table").data("projectid");
        }
    },
    stateSave: true
  });
  
  /*
   * Tasks table, filtered by corpus (For "Corpus Remove" page)
   */
  var corpus_tasks_table = $("#corpus-tasks-table").DataTable({

    columnDefs:[
      {
        targets: 4,
        render: function(data, type, row) {
          return '<a href="/corpora/corpus_preview.php?id='+row[0]+'" title="Preview corpus">'+row[4]+'</a>';
        }
      },


      {
        targets: 9,
        className: "actions",
        sortable: false,
        searchable: false,
     render: function (data, type, row) {
          var actions_str = "";
          actions_str += '<a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span></a>';
          actions_str += '<a href="mailto:' + row[11] + '" title="Contact assigned user"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></a>';
          actions_str += getRemoveTaskCode(row[0], row[2]) ;
          return actions_str;
        }
      }
    ],
    order: [[ 7, 'desc' ]],
    processing: true,
    serverSide: true,
    ajax: {
        url: "/tasks/task_list.php",
        data: function (d) {
          d.corpus_id = $("#corpus-tasks-table").data("corpusid");
        }
    },
    stateSave: true
  });
  // Activate Bootstrap tab on loading or user click.
  
  if (location.hash) {
    $('a[href=\'' + location.hash + '\']').tab('show');
  }
  else {
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
      $('a[href="' + activeTab + '"]').tab('show');
    }
  }

  // Clicking on tab event
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
  
  // Browser History pop state
  $(window).on('popstate', function () {
    var anchor = location.hash || $('a[data-toggle=\'tab\']').first().attr('href');
    $('a[href=\'' + anchor + '\']').tab('show');
  });
  
  /*
   * Invite button. Generates an invitation token.
   */
  $(document).on('click', '#invite_button', function(event){
    event.preventDefault();
    event.stopPropagation();
    if (!$(".form-group").hasClass("has-error")) {
      getInviteToken($('#email').val());
    }
  });
  
  /*
   * Token autocopy button
   */
  $(document).on('click', '#token', function(event) {
    if ($(this).val() === '') return;
    var message_color = $("#helpInvitation").parents(".form-group");
    message_color.removeClass("has-error");
    message_color.removeClass("has-success");
    if (clipboard($(this).val())) {
      message_color.addClass("has-success");
      $("#helpInvitation").html("Copied!");
    }
    else {
      message_color.addClass("has-error");
      $("#helpInvitation").html("Sorry, we cannot copy the text automatically. Please, copy with Ctrl+C.");
    }
    $(this).focus();
  });
  
  $(document).on('click', '.invitation-link', function (event) {

  var email = $(this).parent().siblings(".email").text();
    $("#modal-email").val(email);
    getInviteToken(email);

  });
  
  /*
   * Adds data to the Task remove modal 
   */
  $("#popup_remove_task").on('show.bs.modal', function (event){
    var button = $(event.relatedTarget);
    var task_id = button.data("taskid");
    var username = button.data("username");
    
    var modal = $(this);
    modal.find("#modal-task-id").text(task_id);
    modal.find("#modal-username").text(username);
    modal.find("#task_id").val(task_id);
    
  });
  
  /*
   * Invitation modal
   */
  $("#invite_token_modal").on("hidden.bs.modal", function () {
    var message_color = $("#helpInvitation").parents(".form-group");
    message_color.removeClass("has-error");
    message_color.removeClass("has-warning");
    message_color.removeClass("has-success");
    $("#helpInvitation").html("Please copy this URL and send it to the new user. They will be able to sign up with the token ID.");
    
  });

  $('.collapse')
      .on('shown.bs.collapse', function() {
          $(this).parent()
              .find(".glyphicon-collapse-down")
              .removeClass("glyphicon-collapse-down")
              .addClass("glyphicon-collapse-up");
      })
      .on('hidden.bs.collapse', function() {
          $(this).parent()
              .find(".glyphicon-collapse-up")
              .removeClass("glyphicon-collapse-up")
              .addClass("glyphicon-collapse-down");
      });

    // In order to make data tables become responsive at first load
    $(".dataTable").on("init.dt", () => {
      $(window).trigger('resize');
    });

    // In order to make data tables become responsive at first load
    $('a[data-toggle="tab"]').on('shown.bs.tab', () => {
      $(window).trigger('resize');
    });
    
  /*
   * Calls the function that builds the completion chart for each task
   */    
  for (i in charts) {
    drawPieChart(charts[i]["id"], charts[i]["labels"], charts[i]["data"]);
  }
});

/*
 * Builds the link to remove a task
 */
function getRemoveTaskCode(task_id, username){
  return '<a href="#" data-toggle="modal" data-target="#popup_remove_task" data-taskid='+task_id+' data-username="'+username+ '" title="Remove task"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
  
}

/*
 * Builds the completion chart for a task
 */
function drawPieChart(id, labels, data) {
  var recapChart = new Chart(document.getElementById(id), {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        backgroundColor: ["#F7464A", "#46BFBD", "#FDB45C", "#949FB1", "#3e95cd", "#8e5ea2","#3cba9f","#e2782c", "#4D5360","#20a96a"],
        hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870", "#A8B3C5", "#50aeea", "#c184da", "#46d6b7", "#f38f48", "#616774", "#4bcc91"],
        data: data
      }]
    },
    options: {
      title: {
        display: true,
        text: '# of sentences by type'
      }
    }
  });
}

/*
 * Invites an user, given its email.
 * Provides a link to the user, in case the email was already in use.
 */
function getInviteToken(email){
  var message_color = $("#helpEmail").parents(".form-group");
  message_color.removeClass("has-error");
  message_color.removeClass("has-warning");
  $("#token").val("");
  $("#helpEmail").html();
  $.ajax({
    data: {"email": email},
    url: 'get_token.php', 
    type: 'post',
    dataType: 'json',
    success: function (response) {
      if (response.error === 0) {
        $("#token").val(response.token_url);
        
        if (response.message !== '') {
          $("#helpEmail").html(response.message);
          message_color.addClass("has-warning");
        } else {
          $.ajax({
            data: { to: email, token_url: response.token_url },
            url: '/services/invite_service.php',
            type: 'post',
            dataType: 'json',
            success: (response) => {
              console.log(response);
              if (response.result == true) {
                $("#tokenHelp").html(`We have sent an email with this invite link to ${email}`);
                $("#invitation_url_controls").addClass("has-success");
              } else {
                $("#tokenHelp").html(`We could not send an email to ${email}. You can copy the URL above and send it manually.`);
                $("#invitation_url_controls").addClass("has-error");
              }
            }
          })
        }
      }
      else {
        if ("user_id" in response) {
          $("#helpEmail").html(response.message + " <a href='/admin/user_edit.php?id=" + response.user_id +  "'>Click here to see the profile.</a>");
        }
        else {
          $("#helpEmail").html(response.message);
        }
        message_color.addClass("has-error");
      }
    },
    error: function(response){
      $("#helpEmail").html("Sorry, your request could not be processed. Please, try again later. ");
      message_color.addClass("has-error");
    }
            
  });
};

/*
 * Token autocopy
 */
function clipboard(text) {
  var result = false;
  if (window.clipboardData && window.clipboardData.setData) {
    // IE specific code path to prevent textarea being shown while dialog is visible.
    result = window.clipboardData.setData("Text", text);
  } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
    var textarea = document.createElement("textarea");
    textarea.textContent = text;
    textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
    document.body.appendChild(textarea);
    textarea.select();
    try {
      var result = document.execCommand("copy");  // Security exception may be thrown by some browsers.
    } catch (ex) {
      result = false;
    } finally {
      document.body.removeChild(textarea);
    }
  }
  return result;
};

        

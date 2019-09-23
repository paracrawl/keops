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

Dropzone.options.dropzoneval = { // camelized id
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
        $('#corpora-table-val').DataTable().ajax.reload();
      }, 10000);
      $('#corpora-table-val').DataTable().ajax.reload();
    });

    this.on("canceled", function(file) {
      this.removeFile(file);
    });
  },
  sending: function(file, xhr, formData) {
    formData.append("source_lang", $("#corpora_evaluation .source_lang"));
    formData.append("target_lang", $("#corpora_evaluation .target_lang"));
  },
//  previewTemplate: document.querySelector('#custom-dz-template').innerHTML,
  autoProcessQueue: true
};

Dropzone.options.dropzoneade = { // camelized id
  paramName: "file",
  maxFilesize: 10, // 10 MB
  filesizeBase: 1000,
  previewsContainer: ".dropzone-previews",
  //acceptedFiles: "text/*,application/*",
  //maxFiles: 40, // needed?
  init: function() {
    this.on("complete", function(file) {
      obj = this;

      setTimeout(function() {
        obj.removeFile(file);
        $('#corpora-table-ade').DataTable().ajax.reload();
      }, 10000);
      $('#corpora-table-ade').DataTable().ajax.reload();
    });

    this.on("canceled", function(file) {
      this.removeFile(file);
    });
  },
  sending: function(file, xhr, formData) {
    formData.append("source_lang", $("#corpora_adequacy .source_lang"));
    formData.append("target_lang", $("#corpora_adequacy .target_lang"));
  },
//  previewTemplate: document.querySelector('#custom-dz-template').innerHTML,
  autoProcessQueue: true
};

Dropzone.options.dropzoneflu = { // camelized id
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
        $('#corpora-table-flu').DataTable().ajax.reload();
      }, 10000);
      $('#corpora-table-flu').DataTable().ajax.reload();
    });

    this.on("canceled", function(file) {
      this.removeFile(file);
    });
  },
  sending: function(file, xhr, formData) {
    formData.append("source_lang", $("#corpora_fluency .source_lang"));
  },
//  previewTemplate: document.querySelector('#custom-dz-template').innerHTML,
  autoProcessQueue: true
};

Dropzone.options.dropzoneran = { // camelized id
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
        $('#corpora-table-ran').DataTable().ajax.reload();
      }, 10000);
      $('#corpora-table-ran').DataTable().ajax.reload();
    });

    this.on("canceled", function(file) {
      this.removeFile(file);
    });
  },
  sending: function(file, xhr, formData) {
    formData.append("source_lang", $("#corpora_ranking .source_lang"));
    formData.append("target_lang", $("#corpora_ranking .target_lang"));
  },
//  previewTemplate: document.querySelector('#custom-dz-template').innerHTML,
  autoProcessQueue: true
};


$(document).ready(function() {
    // In order to make data tables become responsive at first load
    $(".dataTable").on("init.dt", () => {
      $(window).trigger('resize');
    });

    // In order to make data tables become responsive at first load
    $('a[data-toggle="tab"]').on('shown.bs.tab', () => {
      $(window).trigger('resize');
    });

    let source_lang = target_lang = -1;
    $(".new-task-form select[name$='_lang'], .new-task-form select[name='mode']").on('change', function() {
      if ($(this).attr('name') == "source_lang") {
        source_lang = $(this).val();
      } else if($(this).attr('name') == "target_lang") {
        target_lang = $(this).val();
      }

      let mode = $("#mode").val();
      if (mode == "FLU") {
        $("#source_lang_group").addClass("d-none");
      } else {
        $("#source_lang_group").removeClass("d-none");
      }

      if (target_lang != -1 && (source_lang != -1 || mode == "FLU")) {
        // We get available users for that language pair
        $.getJSON(`/services/languages_service.php?service=usersByLanguage&source_lang=${source_lang}&target_lang=${target_lang}&mode=${mode}`, ({result, data}) => {
          if (result == 200) {
            $("#assigned_user option").remove();

            if (data.length > 0) {
              $("#assigned_user").removeAttr("disabled");
              $("#helpUsers").addClass("d-none");
              for (user of data) {
                let option = document.createElement('option');
                option.setAttribute('value', user.id);
                option.textContent = user.name;
                $("#assigned_user").append(option);
              }

              $(`#assigned_user option[value=${$("#assigned_user").attr("data-assigned")}]`).prop('selected', true)
              $(`#corpus option[value=${$("#corpus").attr("data-corpus")}]`).prop('selected', true)      
            } else {
              $("#assigned_user").attr("disabled", "");
              $("#helpUsers").removeClass("d-none");
            }
          }
        });

        // We get corpora available for that language pair
        $.getJSON(`/services/corpora_service.php?service=corporaByLanguage&source_lang=${source_lang}&target_lang=${target_lang}&mode=${mode}`, ({result, data}) => {
          if (result == 200) {
            $("#corpus option").remove();

            if (data.length > 0) {
              $("#corpus").removeAttr("disabled");
              $("#helpCorpus").addClass("d-none");
              for (corpus of data) {
                console.log(corpus);
                let option = document.createElement('option');
                option.setAttribute('value', corpus.id);
                option.textContent = `${corpus.name} (${formatDate(corpus.creation_date)})`;
                $("#corpus").append(option);
              }

              $(`#assigned_user option[value=${$("#assigned_user").attr("data-assigned")}]`).prop('selected', true)
              $(`#corpus option[value=${$("#corpus").attr("data-corpus")}]`).prop('selected', true)      
            } else {
              $("#corpus").attr("disabled", "");
              $("#helpCorpus").removeClass("d-none");
            }
          }
        });
      }
    });

    $('body').on('activate.bs.scrollspy', function (e) {
      let active_e = e.target;
      let active_top = $(active_e).offset().top;
      let parent_top = $('#sidenav').offset().top;
      let parent_height = $('#sidenav').height();
      let distance = active_top - parent_top;

      if (distance > parent_height || distance < 0) {
        $('#sidenav ul').scrollTop(distance + $('#sidenav ul').scrollTop());
      }
    })
    
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
      targets: 3,
      render: function (data, type, row) {
        return formatDate(row[3]);
      }
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
      responsivePriority: 1,
     render: function (data, type, row) {
        return '<a href="/projects/project_manage.php?id=' + row[0] + '" title="Manage project">' + row[1] + '</a>';
      }
    },
    {
      targets:3,
      searchable: false,
     render: function (data, type, row) {
       var completed = (parseInt(row[8])/parseInt(row[7])) * 100;
        
        return '<a href="/projects/project_stats.php?id=' + row[0] + '" title="View stats">'+
          '<div title="'+row[8]+' of '+row[7]+' tasks completed"'+'class="progress">' +
          '<div class="progress-bar" role="progressbar" aria-valuenow="' + completed +'"' +
          'aria-valuemin="0" aria-valuemax="100" style="width:' + completed +'%">' +
          '<span>'+row[8] +' of '+ row[7]+'</span></div>' +
          '</div></a>';
       }  
    },
    {
      targets:4,
     render: function (data, type, row) {
       return formatDate(row[3]);
       }
    },
    {
      targets:5,
     render: function (data, type, row) {
       return row [4];
       }
    },
   {
      targets: 6,
      searchable: false,
      className: "text-center",
      render: function (data, type, row) {
        if (row[5]){
          return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
      }
    },
    {
      targets: 7,
      searchable: false,
      className: "actions",
      sortable: false,
      orderable:false,
      responsivePriority: 1,
     render: function (data, type, row) {
       return `<div class="btn-group">
                <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-label="Toggle actions" aria-expanded="false">
                  <span class="glyphicon glyphicon glyphicon-cog"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li><a href="/projects/project_manage.php?id=${row[0]}" title="Manage project's tasks"><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span> Manage project's tasks</a></li>
                  <li><a href="/projects/project_edit.php?id=${row[0]}" title="Edit"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit project</a></li>
                  <li><a href="/projects/project_stats.php?id=${row[0]}" title="View stats"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> View project stats</a></li>
                  <li><a href="/projects/project_remove.php?id=${row[0]}" title="Remove project"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete project</a></li>
                </ul>
              </div>
       `;
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
      searchable: false,
      className: "actions",
      render: function (data, type, row) {
        return `<div class="btn-group">
                  <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-label="Toggle actions" aria-expanded="false">
                    <span class="glyphicon glyphicon glyphicon-cog"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="/languages/language_edit.php?id=${row[0]}" title="Edit language"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit language</a></li>
                  </ul>
                </div>
          `;
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
        targets: 2,
        render: function (data, type, row) {
          return formatDate(row[2]);
        }
      },
      {
        targets: 3,
        searchable: false,
        render: function(data, type, row) {
          return formatDate(row[3]);
        }
      },
      {
        targets: 5,
        searchable: false,
        data: function (row, type, val, meta) {
          return '<a href="/admin/user_edit.php?id=' + row[6] + '" title="Edit user">' + row[5] + '</a>';
        }
      },
      {
        targets: 6,
        className: "actions",
        sortable: false,
        orderable: false,
        responsivePriority: 1,
        searchable: false,
      render: function (data, type, row) {
          str = "";
          if (row[3] == "" || row[3] == null) {
            str = `<li><a href="/admin/revoke_invite.php?id=${row[0]}"><span class="glyphicon glyphicon-remove red" aria-hidden="true" title=\"Revoke invitation\"></span> Revoke invitation</a></li>`;
            str += `<li><a class="invitation-link" data-toggle="modal" data-target="#invite_token_modal"><span class="glyphicon glyphicon-link" aria-hidden="true" title="Get invitation link"></span> Get invitation link</a></li>`;
          }
          else {
            str = `<li class="disabled"><a href="#"><span class="glyphicon glyphicon-remove" aria-hidden="true" title="This user has already accepted the invitation"></span> Remove invitation</a></li>`; 
            str += `<li class="disabled"><a href="#" ><span class="glyphicon glyphicon-link" aria-hidden="true" title="This user has already accepted the invitation"></span> Get invitation link</a></li>`;

         }

         return `<div class="btn-group">
                  <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-label="Toggle actions" aria-expanded="false">
                    <span class="glyphicon glyphicon glyphicon-cog"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right">
                    ${str}
                  </ul>
                </div>
          `;
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
   corpora_table = $("#corpora-table, .corpora-table").DataTable({
    columnDefs: [{
        targets: 1,
        render: function (data, type, row) {
          return '<a href="/corpora/corpus_preview.php?id=' + row[0] + '" title="Preview corpus">' + row[1] + '</a>';
        }
      },
      {
        targets: 2,
        render: function (data, type, row) {
          return (row[2] ? row[2] : "—");
        },
        createdCell: function(td, cellData, rowData, row, col) {
          td.setAttribute('title', (row[2]) ? '' : 'This corpus has no source language because it evaluates fluency');
        }
      },
      {
        targets: 5,
        render: function (data, type, row) {
          return formatDate(row[5]);
        }
      },
      {
        targets: 6,
        render: function (data, type, row) {
          switch (row[6]) {
            case "VAL":
              return "Validation";
            case "ADE":
              return "Adequacy";
            case "FLU":
              return "Fluency";
            case "RAN":
              return "Ranking";
          }
        }
      },
      {
        targets: 7,
        className: "text-center",
        render: function (data, type, row) {
          if (row[7]) {
            return '<span class="glyphicon glyphicon-ok green" aria-hidden="true"></span>';
          } else {
            return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
          }
        }
      },
      {
      targets: 8,
      className: "actions",
      responsivePriority: 1,
      searchable: false,
      render: function (data, type, row) {
        return `<div class="btn-group">
                <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-label="Toggle actions" aria-expanded="false">
                  <span class="glyphicon glyphicon glyphicon-cog"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                  <li><a href="/corpora/corpus_edit.php?id=${row[0]}" title="Edit corpus"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit corpus</a></li>
                  <li><a href="/corpora/corpus_remove.php?id=${row[0]}" title="Remove corpus"><span class="glyphicon glyphicon-trash aria-hidden="true"></span> Remove corpus</a></li>
                </ul>
              </div>
        `;
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
        targets: 2,
        render: function (data, type, row) {
          return (row[2]) ? row[2] : '—';
        },
        createdCell: function(td, cellData, rowData, row, col) {
          if (rowData[10] == "FLU") {
            td.setAttribute('title','This task has no source language because it evaluates fluency');
          }
        }
      },
      {
        targets: 5,
        render: function(data, type, row) {
          return '<a href="/corpora/corpus_preview.php?id='+row[15]+'" title="Preview corpus">'+row[5]+'</a>';
        }
      },
      {
        targets:6,
        render: function (data, type, row) {
          if (row[4] == null || row[6] == "PENDING")
            return row[6];
          if (row[4] == null || row[6] == "DONE")
            return row[6];

          var completed = (parseInt(row[13])/parseInt(row[4])) * 100;        
          return '<a href="/tasks/recap.php?id=' + row[0] + '"><div title="'+row[13]+' of '+row[4]+' sentences evaluated" class="progress">' +
            '<div   class="progress-bar" role="progressbar" aria-valuenow="' + completed +'"' +
            'aria-valuemin="0" aria-valuemax="100" style="width:' + completed +'%">' +
            '<span>'+row[13] +' of '+ row[4]+'</span></div>' +
            '</div></a>';
        }
      },
      {
        targets: 7,
        render: function (data, type, row) {
          return formatDate(row[7]);
        }
      },
      {
        targets: 8,
        render: function (data, type, row) {
          return formatDate(row[8]);
        }
      },
      {
        targets: 9,
        render: function (data, type, row) {
          return formatDate(row[9]);
        }
      },
      {
        targets: 10,
        sortable: true,
        searchable: true,
        render: function (data, type, row) {
          switch (row[10]) {
            case 'VAL':
              return 'Validation';
            case 'ADE':
              return 'Adequacy';
            case 'FLU':
              return 'Fluency';
            case 'RAN':
              return 'Ranking';
          } 
        }
      },
      {
        targets: 11,
        className: "actions",
        sortable: false,
        searchable: false,
        responsivePriority: 1,
        render: function (data, type, row) {
            var actions_str = "";
                        
            if (document.getElementById("input_isowner") && document.getElementById("input_isowner").value == "1") {
              actions_str += '<li><a href="/tasks/change_assigned_user.php?task_id=' + row[0] + '" title="Change assigned user"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Change assigned user</a></li>';
              actions_str += `<li><a href="/tasks/task_dup.php?id=${row[0]}"><span class="glyphicon glyphicon-duplicate"></span> Duplicate task</a></li>`;
            }

            actions_str += '<li><a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Recap of the task</a></li>';
            actions_str += '<li><a href="/contact.php?u='+ row[12] +'" title="Contact assigned user"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Contact assigned user</a></li>';
            actions_str += '<li>' + getRemoveTaskCode(row[0], row[1]) + '</li>';
            
            return `<div class="btn-group">
                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-label="Toggle actions" aria-expanded="false">
                      <span class="glyphicon glyphicon glyphicon-cog"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      ${actions_str}
                    </ul>
                  </div>
            `;
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
        targets: 7,
        render: function (data, type, row) {
          return formatDate(row[6]);
        }
      },
      {
        targets: 8,
        render: function (data, type, row) {
          return formatDate(row[7]);
        }
      },
      {
        targets: 9,
        className: "actions",
        sortable: false,
        searchable: false,
     render: function (data, type, row) {
          var actions_str = "";
          actions_str += '<li><a href="/tasks/recap.php?id=' + row[0] + '" title="Recap of the task"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Recap of the task</a><li>';
          actions_str += '<li><a href="mailto:' + row[11] + '" title="Contact assigned user"><span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Contact assigned user</a><li>';
          actions_str += getRemoveTaskCode(row[0], row[2]) ;

          return `<div class="btn-group">
                    <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-label="Toggle actions" aria-expanded="false">
                      <span class="glyphicon glyphicon glyphicon-cog"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                      ${actions_str}
                    </ul>
                  </div>
            `;
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

  /*
   * Feedback table (for "Feedback" tab)
   */
  let feedback_table = $("#feedback-table").DataTable({
    columnDefs: [
      {
        targets: 1,
        render: function (data, type, row) {
          return `<a href="/admin/user_edit.php?id=${row[5]}">${row[1]}</a>`;
        }
      },
      {
        targets: 3,
        render: function (data, type, row) {
          let mood = '';
          let mood_desc = '';
          switch (row[3]) {
            case 3:
              mood = 'incredible'; mood_desc = 'Awesome'; break;
            case 2:
              mood = 'happy'; mood_desc = 'Good'; break;
            case 1:
              mood = 'sad'; mood_desc = 'Bad'; break;
          }

          return `<img alt="${mood_desc}" style="width:24px; height:24px;" src="/img/feedback_${mood}.png" /> ${mood_desc}`;
        }
      },
      {
        targets: 4,
        class: "multiline"
      },
      {
        targets: 5,
        render: function (data, type, row) {
          return `<a href="/contact.php?u=${row[5]}"><span class="glyphicon glyphicon-envelope"></span> Contact</a>`;
        }
      }
    ],
    order: [[ 0, 'asc' ]],
    processing: true,
    serverSide: true,
    stateSave: true,
    ajax: {
      url: "/services/feedback_service.php",
      data: function (d) {
        d.service = "get";
        d.id = "ALL";
      }
    }
  });

  if ($("input[name='corpus_id']").length > 0) {
    corpora_ade_table.ajax.url(`/services/corpora_service.php?service=ade_description&corpus_id=${$("input[name='corpus_id']").val()}`).load();
  }
  

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
  $("#invitation_form").on('submit', function(event) {
    event.preventDefault();
    event.stopPropagation();
    $("#helpEmail").html("The email should be valid, since it will be used to notify the user");
    let mail = $('#email').val();
    if (mail.match(/(^([\w-]+))(\.([\w-])*)*@([\w-]+)\.(\w+)$/) != null) {
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
    
  /*
   * Calls the function that builds the completion chart for each task
   */
  if (typeof charts != 'undefined') {
    for (i in charts) {
      drawPieChart(charts[i]["id"], charts[i]["labels"], charts[i]["data"]);
    }
  }
});

/*
 * Builds the link to remove a task
 */
function getRemoveTaskCode(task_id, username){
  return '<a href="#" data-toggle="modal" data-target="#popup_remove_task" data-taskid='+task_id+' data-username="'+username+ '" title="Remove task"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Remove task</a>';
  
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
        $(".invitation_token").removeClass("d-none");
        
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
                $("#tokenHelp").html(`<span class='glyphicon glyphicon-envelope'></span> We have sent an email with this invite link to <strong>${email}</strong>`);
                $("#invitation_url_controls").addClass("has-success");
              } else {
                $("#tokenHelp").html(`We could not send an email to <strong>${email}</strong>. You can copy the URL above and send it manually.`);
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

function formatDate(datestring) {
  let date = new Date(datestring);
  if (datestring == null || isNaN(date.getTime())) return "—";

  let day = ((date.getDate() < 10) ? "0" + date.getDate() : date.getDate());
  let month = ((date.getMonth() + 1 < 10) ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1));
  return `${day}.${month}.${date.getFullYear()}`;
}
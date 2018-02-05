$(document).ready(function() {
  var users_table = $("#users-table").DataTable({
    columnDefs: [ {
      targets: 1,
      data: function( row, type, val, meta ) {
        return '<a href="/admin/user_edit.php?id=' + row[0] + '">' + row[1] + '</a>';
      }
    },
    /*{
      targets: 5,
      data: function ( row, type, val, meta ) {
        if (row[5]){
          return '<select class="role-select">\
                    <option ' + (row[5] === "ADMIN" ? "selected" : "") + '>ADMIN</option>\
                    <option ' + (row[5] === "STAFF" ? "selected" : "") + '>STAFF</option>\
                    <option ' + (row[5] === "USER" ? "selected" : "") + '>USER</option>\
                  </select>';
        }
        else {
          return '<span class="glyphicon glyphicon-remove red" aria-hidden="true"></span>';
        }
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
        return '<a href="/admin/projects/project_edit.php?id=' + row[0] + '">' + row[1] + '</a>';
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
});

$(document).ready(function() {
  var users_table = $("#users-table").DataTable({
    columnDefs: [ {
      targets: 1,
      data: function( row, type, val, meta ) {
        return '<a href="/admin/edit_user.php?id=' + row[0] + '">' + row[1] + '</a>';
      }
    },
    {
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
    },
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
    ajax: "/users/list_users.php"
  });
});

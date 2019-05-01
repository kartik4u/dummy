

$(document).on('click', '.userdel', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    swal({
      title: 'Are you sure?',
     text: "You won't be able to revert this!",
     type: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     confirmButtonClass: 'btn btn-success',
     cancelButtonClass: 'btn btn-danger',
     buttonsStyling: false
        },
        function() {

            $.ajax({
                type: "get",
                url: "manageAssistants/delete",
                data: {id:id},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    swal(
                      'Deleted!',
                      response["response"],
                      'success'
                    )
                    setTimeout(function(){
                        location.reload();
                      }, 2000);

                  }else{
                    swal(
                      'Error',
                      response["response"],
                      'error'
                    )
                  }

                    }
            });
    });
});

$(document).on('click', '.userstatus', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var status = $(this).data('status');
if(status == 1)
{
  status_value = "activate";
 
}
else
{
      status_value = "deactivate";
      
}
    swal({
            title: "Are you sure you want to " + status_value + " the user?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {

            $.ajax({
                type: "get",
                url: "manageAssistants/status",
                data: {id:id, status:status},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    swal(
                      'Success',
                      response["response"],
                      'success'
                    )
                    setTimeout(function(){
                        location.reload();
                      }, 2000);

                  }else{
                    swal(
                      'Error',
                      response["response"],
                      'error'
                    )
                  }

                    }
            });
    });
});



$(document).on('click', '.userverify', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var post_verify = $(this).data('post_verify');
if(post_verify == 0)
{
  status_value = "verify";
  post_verify = 1;
}
else
{
      status_value = "un-verify";
      post_verify = 0;
}
    swal({
            title: "Are you sure you want to " + status_value + " the user?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {

            $.ajax({
                type: "get",
                url: "manageAssistants/verifyUserPost",
                data: {id:id, post_verify:post_verify},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    swal(
                      'Success',
                      response["response"],
                      'success'
                    )
                    setTimeout(function(){
                        location.reload();
                      }, 2000);

                  }else{
                    swal(
                      'Error',
                      response["response"],
                      'error'
                    )
                  }

                    }
            });
    });
});



$(document).on('click', '.useremail', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var status = $(this).data('status');

if(status == 0)
{
  status_value = "activate";
  status = 1;
}
else
{
      status_value = "deactivate";
      status = 0;
}



    swal({
            title: "Are you sure you want to " + status_value + " email?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {

            $.ajax({
                type: "get",
                url: "manageAssistants/emailStatus",
                data: {id:id, status:status},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    swal(
                      'Success',
                      response["response"],
                      'success'
                    )
                    setTimeout(function(){
                        location.reload();
                      }, 2000);

                  }else{
                    swal(
                      'Error',
                      response["response"],
                      'error'
                    )
                  }

                    }
            });
    });
});


$(document).on('click', '.userdeldetail', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    getUrl = window.location.origin;

    swal({
      title: 'Are you sure?',
     text: "You won't be able to revert this!",
     type: 'warning',
     showCancelButton: true,
     confirmButtonColor: '#3085d6',
     cancelButtonColor: '#d33',
     confirmButtonText: 'Yes, delete it!',
     cancelButtonText: 'No, cancel!',
     confirmButtonClass: 'btn btn-success',
     cancelButtonClass: 'btn btn-danger',
     buttonsStyling: false
        },
        function() {

            $.ajax({
                type: "get",
                url: "../manageAssistants/delete",
                data: {id:id},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    swal(
                      'Deleted!',
                      response["response"],
                      'success'
                    )
                    setTimeout(function(){
                       window.location.href= getUrl+"/admin/manageAssistants"
                      }, 2000);

                  }else{
                    swal(
                      'Error',
                      response["response"],
                      'error'
                    )
                  }

                    }
            });
    });
});

$(document).on('click', '.u_status_single', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var status = $(this).data('status');
if(status == 1)
{
  status_value = "activate";
 
}
else
{
      status_value = "deactivate";
      
}
    swal({
            title: "Are you sure you want to " + status_value + " the user?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {

            $.ajax({
                type: "get",
                url: "../manageAssistants/status",
                data: {id:id, status:status},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    swal(
                      'Success',
                      response["response"],
                      'success'
                    )
                    setTimeout(function(){
                        location.reload();
                      }, 2000);

                  }else{
                    swal(
                      'Error',
                      response["response"],
                      'error'
                    )
                  }

                    }
            });
    });
});

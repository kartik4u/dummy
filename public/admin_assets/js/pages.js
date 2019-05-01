

$(document).on('click', '.pagedel', function (e) {
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
                url: "managePages/delete",
                data: {id:id},
                success: function (data) {

                  var response = JSON.parse(data);


                  if(response["status"] == 200){

                    setTimeout(function(){
                      swal(
                        'Success',
                        response["response"],
                        'success'
                      )

                      }, 200);
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

$(document).on('click', '.pagestatus', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var status = $(this).data('status');
if(status == 0)
{
  status_value = "activate";
  status = 1;
}
else {
      status_value = "deactivate";
      status = 0;
}
    swal({
            title: "Are you sure you want to " + status_value + " page?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {

            $.ajax({
                type: "get",
                url: "managePages/status",
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

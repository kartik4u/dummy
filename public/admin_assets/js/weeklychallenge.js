

$(document).on('click', '.catdel', function (e) {
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
                url: "weeklyChallenge/delete",
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

$(document).on('click', '.subcatstatus', function (e) {
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
            title: "Are you sure you want to " + status_value + " weekly challenge?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function (val) {

                if (val == true) {
                    manageSubCategory(id, status);
                }
            });
});




function manageSubCategory(id, status) {
    $.ajax({
        type: "get",
        url: "weeklyChallenge/status",
        data: {id: id, status: status},
        success: function (data) {

            var response = JSON.parse(data);

            if (response.status == 200) {

                setTimeout(function () {
                    swal("Success", response.response, "success");
                }, 1000);

            } else {
                setTimeout(function () {
                    swal("Error", response.response, "error");
                }, 1000);
            }
            setTimeout(function () {
                location.reload();
            }, 3000);
        }
    });
}

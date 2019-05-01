

$(document).on('click', '.poststatus', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var status = $(this).data('status');
if(status == 1)
{
  status_value = "Enable";
 
}
else
{
      status_value = "Disable";
      
}
    swal({
            title: "Are you sure you want to " + status_value + " the post?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {

            $.ajax({
                type: "get",
                url: "managePosts/status",
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
 


$(document).on('click', '.postverify', function (e) {
    e.preventDefault();
    var id = $(this).data('id');
    var status = $(this).data('status');
if(status == 1)
{
  status_value = "Verify";
  status = 1;
}
else
{
      status_value = "Decline";
      status = 4;
}
    swal({
            title: "Are you sure you want to " + status_value + " the post?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes!",
            showCancelButton: true,
        },
        function() {
            $.ajax({
                type: "get",
                url: "managePosts/verifyPost",
                data: {id:id, status:status},
                beforeSend: function () {
                    setTimeout(function(){ 
                        swal({title: "Loading....", text: "<div style=\"z-index:2000\" class=\"progress\"><div class=\"progress-bar progress-bar-striped progress-bar-animated active\" role=\"progressbar\" aria-valuenow=\"75\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 100%\"></div></div>", html: true, showConfirmButton: false, customClass: "swal-loader"});
                    }, 500);                    
                },
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


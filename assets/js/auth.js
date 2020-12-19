$(()=>{
    var authenticate = {
        init:()=>{
            if(localStorage.getItem('session') != null){
                session = JSON.parse(localStorage.getItem('session'));
                if(session.key != null){
                    window.location.href = 'dashboard';
                }else{
                    localStorage.clear();
                }
            }
            ajaxAddOn.removeFullPageLoading();
        },
        ajax:{
            login:(payload)=>{
                $("#frm-send-password").find("div.alert").remove()
                $("#frm-send-password").find(":input").prop({"disabled":"disabled"})
                $("#frm-send-password").find("button").prepend(
                    $("<i>")
                        .addClass("fas fa-spinner fa-spin"),
                )
                ajaxAddOn.ajaxForm({
                    type:'POST',
                    url:loginApi,
                    dataType:'json',
                    payload:payload,
                }).then(response=>{
                    
                    if(!response.isError){
                        localStorage.setItem('session',JSON.stringify(response.data[0]));
                        window.location.href = 'dashboard';
                    }
                    ajaxAddOn.swalMessage(response.isError,response.message)
                    $("#frm-send-password").find(":input").prop({"disabled":""})
                    $("#frm-send-password").find("button").find("i.fa-spinner").remove();
                    
                })
            },
            getPassword:(payload)=>{
                ajaxAddOn.ajaxForm({
                    type:'POST',
                    url:getPasswordApi,
                    dataType:'json',
                    payload:payload,
                }).then(response=>{
                    console.log("response",response)
                    if(!response.isError){
                        $("#frm-send-password")[0].reset();
                    }
                    $(".modal").modal("hide")
                    ajaxAddOn.swalMessage(response.isError,response.message)
                })
            }
        },
        display:{
            initTour:()=>{
                var tour = new Tour({
                    steps: [
                      {
                        element: "#email",
                        title: "1st Panel",
                        content: "This is the first panel",
                        placement: "bottom"
                      },
                      {
                        element: "#password",
                        title: "1st Panel",
                        content: "This is the second panel",
                        placement: "bottom"
                      },
                      {
                        element: "#login",
                        title: "1st Panel",
                        content: "This is the third panel",
                        placement: "bottom"
                      }
                    ],
                    backdrop: true,
                    storage: false
                  });
                  
                  // tour.init();
                  tour.start();
                //   tour.init();
                  // tour.start();
            }
        }
    }
    authenticate.init();
    $("#frm_login").validate({
        errorElement: 'span',
		errorClass: 'text-danger',
	    highlight: function (element, errorClass, validClass) {
	      $(element).closest('.form-group').addClass("has-warning");
	      $(element).closest('.form-group').find("input").addClass('is-invalid');
	      $(element).closest('.form-group').find("select").addClass('is-invalid');
	    },
	    unhighlight: function (element, errorClass, validClass) {
	      $(element).closest('.form-group').removeClass("has-warning");
	      $(element).closest('.form-group').find("input").removeClass('is-invalid');
	      $(element).closest('.form-group').find("select").removeClass('is-invalid');
	    },
        rules:{
            email:{
                required:true,
            },
            password:{
                required:true,
            },
            // password:{
            //     required:true,
            // },
        },
        submitHandler: function(form) {
            let payload = $(form).serialize()
            authenticate.ajax.login(payload);
        }
    })
    $("#frm-send-password").validate({
        errorElement: 'span',
		errorClass: 'text-danger',
	    highlight: function (element, errorClass, validClass) {
	      $(element).closest('.form-group').addClass("has-warning");
	      $(element).closest('.form-group').find("input").addClass('is-invalid');
	      $(element).closest('.form-group').find("select").addClass('is-invalid');
	    },
	    unhighlight: function (element, errorClass, validClass) {
	      $(element).closest('.form-group').removeClass("has-warning");
	      $(element).closest('.form-group').find("input").removeClass('is-invalid');
	      $(element).closest('.form-group').find("select").removeClass('is-invalid');
	    },
        rules:{
            email:{
                required:true,
            },
            // password:{
            //     required:true,
            // },
        },
        submitHandler: function(form) {
            authenticate.ajax.getPassword($(form).serialize());
        }
    })
    $("#starter").click(function(e){
        e.preventDefault();
        authenticate.display.initTour();
    })
    window.addEventListener('beforeunload',(event) =>{
        if(localStorage.getItem('session') != null){
            ajaxAddOn.setSessionData('session',session);
        }
    });


   
})


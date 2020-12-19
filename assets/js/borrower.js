$(()=>{
    var  getApiData  = JSON.parse(localStorage.getItem("session"));
    borrower = {
        init:()=>{
            borrower.ajax.getBorrower({
                borrower_id:getApiData.borrower_id
            })
            borrower.ajax.getBorrowerSchedules({
                borrower_id:getApiData.borrower_id
            })
        },
        ajax:{
            getBorrower:(payload)=>{
                ajaxAddOn.ajax({
                    type:'POST',
                    url:getBorrowerInfoApi,
                    payload:payload,
                    dataType:'json',
                }).then(response=>{
                    if(!response.isError){
                        // console.log("response",response)
                        $("#profile_name").append($("<b>").text("Name: "),`${ajaxAddOn.capitalize(response.data.Name)}`);
                        $("#profile_district").append($("<b>").text("District: "),`${ajaxAddOn.capitalize(response.data.district_name)}`);
                        $("#profile_gender").append($("<b>").text("Gender: "),`${ajaxAddOn.capitalize(response.data.gender)}`);
                        $("#profile_email").append($("<b>").text("Email: "),`${response.data.email}`);
                        $("#profile_phone").append($("<b>").text("Mobile: "),`${ajaxAddOn.capitalize(response.data.mobile)}`);
                        $("#profile_telephone").append($("<b>").text("Telephone: "),`${ajaxAddOn.capitalize(response.data.telephone)}`);
                        $("#profile_address").append($("<b>").text("Address: "),`${ajaxAddOn.capitalize(response.data.present_address)}`);
                        $("#profile_position").append($("<b>").text("Position: "),`${ajaxAddOn.capitalize(response.data.present_address)}`);
                        $(".acount_username").append(`${response.data.username}`);
                        $("#profile-img").attr({
                            src:`${baseUrl}/uploads/${getApiData.borrower_id}/${getApiData.image}`
                        })
                    }else{
                        ajaxAddOn.swalMessage(!response.isError,response.message);
                    }
                    ajaxAddOn.removeFullPageLoading();
                })
            },
            getBorrowerSchedules:(payload)=>{
                ajaxAddOn.ajax({
                    type:'POST',
                    url:getBorrowerScheduleApi,
                    payload:payload,
                    dataType:'json',
                }).then(response=>{
                    if(!response.isError){
                        $("#table-schedule tbody").empty()
                        $.each(response.data,function(k,v){
                           $("#table-schedule tbody")
                            .append(
                                $("<tr>")
                                    .append(
                                        $("<td>")
                                            .append(
                                                $("<span>")
                                                    .addClass("float-right font-weight-bold")
                                                    .text(moment(v.start).format("dddd MMM DD, YYYY h:mm:ss a")),
                                                v.title
                                            )
                                    )
                            )
                       })
                    }else{
                        ajaxAddOn.swalMessage(!response.isError,response.message);
                    }
                    ajaxAddOn.removeFullPageLoading();
                })
            },
            changePassword:(payload)=>{
                ajaxAddOn.ajaxForm({
                    type:'POST',
                    url:changePasswordApi,
                    payload:payload,
                    dataType:'json',
                }).then(response=>{
                    if(!response.isError){
                        $("#frm-change-password")[0].reset();
                        $(".modal").modal("hide")
                    }
                    ajaxAddOn.swalMessage(response.isError,response.message);
                    ajaxAddOn.removeFullPageLoading();
                })
            },
            changeUserName:(payload)=>{
                ajaxAddOn.ajaxForm({
                    type:'POST',
                    url:changeUserNameApi,
                    payload:payload,
                    dataType:'json',
                }).then(response=>{
                    if(!response.isError){
                        $("#frm-change-username")[0].reset();
                        $(".modal").modal("hide")
                    }
                    ajaxAddOn.swalMessage(response.isError,response.message);
                    ajaxAddOn.removeFullPageLoading();
                })
            },
        },
    }
    borrower.init();
    $("#frm-change-password").validate({
        errorElement: 'span', //change error placement to span
        errorClass: 'text-danger', // add error class to text danger on bootstrap
        highlight: function (element, errorClass, validClass) {  //init hightligh function
            $(element).closest('.form-group').removeClass("has-success"); // removed class success if the data is error
            $(element).closest('.form-group').addClass("has-error"); // add error class
            $(element).addClass("is-invalid"); // add error class
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass("has-error"); // removed class error if the data is error
            $(element).closest('.form-group').addClass("has-success"); // add success class
            $(element).removeClass("is-invalid"); // add error class
        },
        rules:{ // initialize rules
            oldpassword:{
                required:true, // add required
            },
            password:{
                required:true, // add required
                minlength : 6,
            },
            password_confirm : {
                minlength : 6,
                equalTo : "#password"
            }
        },
        submitHandler:function(form){ // submit function if the all rules is true
            password = $(form).find(":input[name=password]").val();
            oldpassword = $(form).find(":input[name=oldpassword]").val();
            borrower_id = getApiData.borrower_id;
            payload = {
                borrower_id:borrower_id,
                password:password,
                oldpassword:oldpassword,
            }
            borrower.ajax.changePassword(payload)

        }
    })
    $("#frm-change-username").validate({
        errorElement: 'span', //change error placement to span
        errorClass: 'text-danger', // add error class to text danger on bootstrap
        highlight: function (element, errorClass, validClass) {  //init hightligh function
            $(element).closest('.form-group').removeClass("has-success"); // removed class success if the data is error
            $(element).closest('.form-group').addClass("has-error"); // add error class
            $(element).addClass("is-invalid"); // add error class
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass("has-error"); // removed class error if the data is error
            $(element).closest('.form-group').addClass("has-success"); // add success class
            $(element).removeClass("is-invalid"); // add error class
        },
        rules:{ // 
            username:{
                required:true, // add required
                minlength : 6,
            },
        },
        submitHandler:function(form){ // submit function if the all rules is true
            username = $(form).find(":input[name=username]").val();
            borrower_id = getApiData.borrower_id;
            payload = {
                borrower_id:borrower_id,
                username:username,
            }
            borrower.ajax.changeUserName(payload)

        }
    })
})
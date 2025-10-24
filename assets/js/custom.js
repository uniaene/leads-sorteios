// change name when file is selected
$("#attachment").on('change', function (e) {
  // alert("file is selected");
  var filename = e.target.files[0].name;
  $(".attachments label").text(filename);
});



$(document).ready(function () {
  var ImgboxID = 1;
  $(".guests").on('change', function () {

    const [file] = addguest.files
    if (file) {
      var imgsrc = this.src = window.URL.createObjectURL(file);

    }
    console.log(imgsrc);


    // var ImgBox = "<img alt=\"Guest " + ImgboxID + "\">";
    // ImgBox.setAttribute('src', imgsrc);
    var ImgBoxDiv = $(document.createElement('div'))
      .attr({
        class: 'guest-single'
      });
    var createElement = $(document.createElement('img'))
      .attr(
        {
          src: imgsrc,
          alt: 'Guest ' + ImgboxID

        }
      );
    ImgBoxDiv.after().html(createElement);

    ImgBoxDiv.appendTo(".guest-inner");

    ImgboxID++;

  })
})


var textboxID = 2;

$("#notifiymail").on('click', function () {
  var textbox = "<div class='text-input'>\
                            <label for=\"notifiy_mail" + textboxID + "\">Notification</label>\
                            <input type='text' name=\"notifiy_mail" + textboxID + "\" id=\"notifiy_mail" + textboxID + "\" placeholder=\"Email" + textboxID + "\">\
                        </div>";



  var TextBoxDiv = $(document.createElement('div'))
    .attr({
      class: 'tab-100 col-md-6'
    });

  TextBoxDiv.after().html(textbox);

  TextBoxDiv.appendTo("#notifyemail");

  textboxID++;
});





// disable on enter
$('form').on('keyup keypress', function (e) {
  var keyCode = e.keyCode || e.which;
  if (keyCode === 13) {
    e.preventDefault();
    return false;
  }
});




// form validiation
var inputschecked = false;


function formvalidate(stepnumber) {
  // check if the required fields are empty
  inputvalue = $("#step" + stepnumber + " :input").not("button").map(function () {
    if (this.value.length > 0) {
      $(this).removeClass('invalid');
      return true;

    }
    else {

      if ($(this).prop('required')) {
        $(this).addClass('invalid');
        return false
      }
      else {
        return true;
      }

    }
  }).get();


  // console.log(inputvalue);

  inputschecked = inputvalue.every(Boolean);

  // console.log(inputschecked);
}


$(document).ready(function () {
  $("#sub").on('click', function () {
    var value = $("#notifyemail :input").map(function () {
      return this.value;
    }).get().join(", ");

    var email = $("#mail-email").val();

    //number validiation
    var numbers = /^[0-9]+$/;

    //email validiation
    var re = /^\w+([-+.'][^\s]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    var emailFormat = re.test(email);


    formvalidate(1);


    if (inputschecked == false) {
      formvalidate(1);
    }

    // check if email is valid
    else if (emailFormat == false) {
      // console.log("enter valid email address");
      (function (el) {
        setTimeout(function () {
          el.children().remove('.reveal');
        }, 3000);
      }($('#error').append('<div class="reveal alert alert-danger">Digite um e-mail válido!</div>')));
      if (emailFormat == true) {
        $("#mail-email").removeClass('invalid');
      }
      else {
        $("#mail-email").addClass('invalid');
      }
    }

    else {
      $("#sub").html("<img src='assets/images/loading.gif'>");



      var dataString = new FormData(document.getElementById("steps"));


      console.log(dataString);


      // send form to send.php
      $.ajax({
        type: "POST",
        url: "form handling/send.php",
        data: dataString,
        processData: false,
        contentType: false,
        cache: false,
        success: function (data, status) {

          $("#sub").html("Success!");
          console.log(data);

          // window.location = "thankyou.html";

        },
        error: function (data, status) {
          $("#sub").html("failed!");
          console.log(data);
        }
      });
    }

  });
}
);































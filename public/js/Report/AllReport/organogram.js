(function ($, app) {
  'use strict';
  $(document).ready(function () {
    var $numClick = 1;
    $("#nepaliChart").hide();
    $("#nepaliBtn").on('click', function (){
        $numClick++;
        if ($numClick%2 ==0 ) {
            $("#nepaliChart").show();
            $("#englishChart").hide();
          } else {
            $("#nepaliChart").hide();
            $("#englishChart").show();
          }
    });

    var element = $("#imageDIV"); // global variable
    var getCanvas; // global variable
    // html2canvas(element, {
    //   onrendered: function (canvas) {
    //     $("#previewImage").append(canvas);
    //     getCanvas = canvas;
    //   }
    // });
  //   html2canvas(document.body).then(function(canvas) {
  //     var link = document.createElement("a");
  //     document.body.appendChild(link);
  //     link.download = "html_image.png";
  //     link.href = canvas.toDataURL("image/png");
  //     link.target = '_blank';
  //     link.click();
  // });
    $("#btnDownloadImage").on('click', function () {
      if ($numClick%2 ==0 ) {
        $("#previewImageNep").addClass('shrink');
        html2canvas($("#previewImageNep")).then(function(canvas) {
          var link = document.createElement("a");
          document.body.appendChild(link);
          link.download = "html_image.png";
          link.href = canvas.toDataURL("image/png");
          link.target = '_blank';
          link.click();
        $("#previewImageNep").removeClass('shrink');
        });
        $("#previewImageNep").removeClass('shrink');
      }else{
        $("#previewImage").addClass('shrink');
        html2canvas($("#previewImage")).then(function(canvas) {
          var link = document.createElement("a");
          document.body.appendChild(link);
          link.download = "html_image.png";
          link.href = canvas.toDataURL("image/png");
          link.target = '_blank';
          link.click();
        $("#previewImage").removeClass('shrink');
        });
        $("#previewImage").removeClass('shrink');
      }
    });
  });
})(window.jQuery, window.app);
$(function () {
    $('.carousel').carousel({
        interval: 5000
    });

    var anchor = $("a.tab-anchor");

    anchor.on('click', function (e) {
        e.preventDefault();
    });

    // --------------------------Trigger File upload browsing Section ---------------------------

    var uploadBtn = $('.upload');
    uploadBtn.click(function () {
        var uploadInp = $('input[type=file]');
        uploadInp.change(function () {
            if (validateImgFile(this)) {
                previewURL(this);
            }
        }).click();
    });

    function previewURL(input) {

        if (input.files && input.files[0]) {

            // collecting the file source
            var file = input.files[0];

            // preview the image
            var reader = new FileReader();
            reader.onload = function (e) {
                var src = e.target.result;
                $("#img").attr('src', src);
            }
            reader.readAsDataURL(file);

        }
    }


    //validating the file

    function validateImgFile(input) {
        if (input.files && input.files[0]) {

            // collecting the file source
            var file = input.files[0];

            // validating the image name
            if (file.name.length < 1) {
                alert("The file name couldn't be empty");
                return false;
            }
            // validating the image size
            else if (file.size > 100000) {
                alert("The file is too big");
                return false;
            }
            // validating the image type
            else if (file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg') {
                alert("The file does not match png, jpg or gif");
                return false;
            }
            return true;
        }
    }
});


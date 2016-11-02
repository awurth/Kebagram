
$(document).ready(function () {

    var jcrop_api;

    var container = $('#picture-container');
    var containerWidth = container.width();
    var cropPicContainer = $('#crop-pic-container');
    var cropPicInput = $('#crop-pic');

    var xInput = $('#pic-x');
    var yInput = $('#pic-y');
    var x2Input = $('#pic-x2');
    var y2Input = $('#pic-y2');
    var widthInput = $('#pic-width');
    var heightInput = $('#pic-height');
    var originalWidthInput = $('#original-width');
    var originalHeightInput = $('#original-height');

    var noCrop = function () {
        cropPicInput.prop('checked', false);
    };

    var setCoords = function (c) {
        xInput.val(c.x);
        yInput.val(c.y);
        x2Input.val(c.x2);
        y2Input.val(c.y2);
        widthInput.val(c.w);
        heightInput.val(c.h);

        cropPicInput.prop('checked', true);
    };

    var getSelectCoords = function (width, height) {
        var x, y, x2, y2;

        if (width > height) {
            x = (width / 2) - (height / 2);
            y = 0;
            x2 = height + x;
            y2 = height;
        } else if (width < height) {
            x = 0;
            y = (height / 2) - (width / 2);
            x2 = width;
            y2 = width + y;
        } else {
            x = 0;
            y = 0;
            x2 = width;
            y2 = height;
        }

        return [x, y, x2, y2];
    };

    $('#picture-file').change(function (e) {
        container.empty();
        container.append('<div id="picture-thumb"></div>');

        var thumb = container.children('#picture-thumb');

        loadImage(
            e.target.files[0],
            function (img) {
                var coords = getSelectCoords(img.width, img.height);

                originalWidthInput.val(img.width);
                originalHeightInput.val(img.height);

                thumb.html(img);
                thumb.Jcrop({
                    aspectRatio: 1,
                    setSelect: coords,
                    onChange: setCoords,
                    onSelect: setCoords,
                    onRelease: noCrop
                }, function () {
                    jcrop_api = this;
                });
            },
            {maxWidth: containerWidth}
        );

        cropPicContainer.show();

    });

    cropPicInput.change(function () {
        if ($(this).prop('checked')) {
            var coords = getSelectCoords(originalWidthInput.val(), originalHeightInput.val());
            jcrop_api.setSelect(coords);
        } else {
            jcrop_api.release();
        }

        return false;
    });

});

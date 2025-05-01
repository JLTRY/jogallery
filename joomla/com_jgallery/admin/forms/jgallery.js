jQuery(function() {
    document.formvalidator.setHandler('directory',
        function (value) {
            regex=/^[^0-9]+$/;
            return regex.test(value);
        });
});
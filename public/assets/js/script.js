$(document).ready(function() {
    let focusedInput = $('[name="item_barcode"]');

    $(document).on('focus', 'input[type="number"]', function() {
        $('#keypad').show();
        focusedInput = $(this);
    });

    $(document).on('blur', 'input[type="number"]', function() {
        setTimeout(function() {
            if (!focusedInput.is(":focus") && !$('#keypad').is(":focus")) {
                $('#keypad').hide();
                focusedInput = null;
            }
        }, 100);
    });

    $('#keypad .key').on('click', function(event) {
        event.stopPropagation();
        if (focusedInput) {
            var value = $(this).data('value');
            focusedInput.val(focusedInput.val() + value);
        }
    });

    $('#clearKey').on('click', function(event) {
        event.stopPropagation();
        if (focusedInput) {
            focusedInput.val('');
        }
    });

    $('#keypad').on('mousedown', function(event) {
        event.preventDefault();
    });
});

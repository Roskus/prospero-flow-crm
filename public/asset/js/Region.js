$(document).ready(function() {
    $('#country_id').change(function() {
        let countryId = $(this).val();
        if (countryId) {
            $.ajax({
                url: '/ajax/region/' + countryId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#province').empty().append('<option value="">Select a province</option>');
                    $.each(data.regions, function(key, value) {
                        $('#province').append('<option value="' + key + '">' + value + '</option>');
                    });
                    $('#province').prop('disabled', false);
                }
            });
        } else {
            $('#province').empty().append('<option value="">Select a province</option>').prop('disabled', true);
        }
    });
});

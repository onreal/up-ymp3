jQuery(document).ready(function($) {
    $('#upymp3-submit').click(function() {
        var url = $('#upymp3-url').val();
        if (!url) return alert('Please enter a URL.');

        $('#upymp3-status').text('Adding to queue...');
        $('#upymp3-loader').show();

        $.post(upymp3_ajax.ajax_url, {
            action: 'upymp3_convert',
            nonce: upymp3_ajax.nonce,
            url: url
        }, function(response) {
            $('#upymp3-loader').hide();

            if (response.success) {
                $('#upymp3-status').text('Processing...');
                checkJobStatus(response.data.job_id);
            } else {
                $('#upymp3-status').text('Error: ' + response.data);
            }
        });
    });

    function checkJobStatus(job_id) {
        $.post(upymp3_ajax.ajax_url, {
            action: 'upymp3_check_status',
            nonce: upymp3_ajax.nonce,
            job_id: job_id
        }, function(response) {
            if (response.success) {
                if (response.data.status === 'completed') {
                    $('#upymp3-status').html(`<a class="upymp3-download" href="${response.data.file_url}" download>Download MP3</a>`);
                } else {
                    setTimeout(() => checkJobStatus(job_id), 3000);
                }
            } else {
                $('#upymp3-status').text('Error checking status.');
            }
        });
    }
});

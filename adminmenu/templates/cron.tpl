<form class="someform" action="{$postURL}" id="runCronForm">
    {$jtl_token}
    <input type="hidden" name="runCron" value="1">

    <div class="messages">
        {if empty($setting['csv_path']) || empty($setting['csv_name'])}
            <h4>Fix Below errors:</h4>
            {if empty($setting['csv_path'])}
                <div class="alert alert-danger">Please update csv path in setting.</div>
            {/if}
            {if empty($setting['csv_name'])}
                <div class="alert alert-danger">Please update csv file name in setting.</div>
            {/if}
        {/if}
    </div>

    {if !empty($setting['csv_path']) && !empty($setting['csv_name'])}
        <button type="button" onclick="runCronManual()" class="btn btn-primary">Run Cron Manually</button>
    {/if}


</form>
<script>
    function runCronManual() {
        $('.messages').html('');
        $.ajax({
            url: $('#runCronForm').attr('action'),
            type: 'POST',
            data: $('#runCronForm').serialize(),
            success: function(response) {
                if (response.flag) {
                    let successHtml = '<ul class="alert alert-success">';
                    successHtml += `<li>` + response.message + `</li>`;
                    successHtml += "</ul>";
                    $('.messages').html(successHtml);
                } else {
                    let errorsHtml = '<ul class="alert alert-danger">';
                    $.each(response.errors, function(i, v) {
                        errorsHtml += `<li>` + v + `</li>`;
                    });
                    errorsHtml += "</ul>";
                    $('.messages').html(errorsHtml);
                }
            }
        });
    }
</script>

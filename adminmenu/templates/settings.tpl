<form class="someform" action="{$postURL}" id="saveSettingForm">
    {$jtl_token}
    <input type="hidden" name="saveSetting" value="1">

    <div class="messages">
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">{$langVars->getTranslation('csv_path')|to_charset:"UTF-8"}</label>
                <input type="text" class="form-control" name="csv_path" id="csv_path" value="{$setting['csv_path']}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">{$langVars->getTranslation('csv_name')|to_charset:"UTF-8"}</label>
                <input type="text" class="form-control" name="csv_name" id="csv_name" value="{$setting['csv_name']}">
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label">{$langVars->getTranslation('categories')|to_charset:"UTF-8"}</label>
                <select class="form-control" name="allowed_categories[]" id="allowed_categories" multiple>
                    <option value="0" {if in_array(0, $selected_categories)}selected{/if}>All</option>
                    {foreach $categories as $category}
                        <option value="{$category->kKategorie}"
                            {if in_array($category->kKategorie, $selected_categories)}selected{/if}>{$category->cName}
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="label">{$langVars->getTranslation('plugin_status')|to_charset:"UTF-8"}</label>
                <select class="form-control" name="plugin_status" id="plugin_status">
                    <option value="1" {if $setting["plugin_status"]}selected{/if}>Active</option>
                    <option value="0" {if !$setting["plugin_status"]}selected{/if}>In Active</option>
                </select>
            </div>
        </div>

    </div>


    <button type="button" onclick="updateSetting()" class="btn btn-primary">Save</button>

</form>
<script>
    $(document).ready(function() {
        $('#allowed_categories').select2();
    });

    function updateSetting() {
        $('.messages').html('');
        $.ajax({
            url: $('#saveSettingForm').attr('action'),
            type: 'POST',
            data: $('#saveSettingForm').serialize(),
            success: function(response) {
                if (response.flag) {
                    let successHtml = '<ul class="alert alert-success">';
                    successHtml += `<li>Setting updated.</li>`;
                    successHtml += "</ul>";
                    $('.messages').html(successHtml);
                    setTimeout(function() { window.location.reload(); }, 1000);
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

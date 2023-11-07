<form method="post" enctype="multipart/form-data" class="form-horizontal" name="importMotorPartCsvForm"
    id="importMotorPartCsvForm" action="{$postURL}">
    <div _mstvisible="3">
        {$jtl_token}

        <div class="form-group form-row align-items-center">
            <label class="col col-sm-4 col-form-label text-sm-right" for="csv">Motorcycle Part CSV</label>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                <input type="file" name="import_motor_part_file" id="import_motor_part_file" class="form-control"
                    tabindex="1" required />
            </div>
        </div>

        <div class="form-group form-row align-items-center">
            <label class="col col-sm-4 col-form-label text-sm-right" for="delete_old">Datensatz überschreiben</label>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                <input type="checkbox" name="delete_old" id="delete_old" class="" value="1" tabindex="2" />
            </div>
            <div class="col-sm pl-sm-3 pr-sm-5 order-last order-sm-2">
                <a href="{\URL_SHOP}/plugins/cb_motor_search/sample.csv"><i class="fa fa-download"></i> Download Smaple
                    CSV</a>
            </div>
        </div>

        <progress id="progressBar" value="0" max="100" style="width:300px;"></progress>
        <h3 id="status"></h3>
        <p id="loaded_n_total"></p>
    </div>
    <div class="card-footer save-wrapper" _mstvisible="3">
        <div class="row" _mstvisible="4">
            <div class="ml-auto col-sm-6 col-xl-auto" _mstvisible="5">
                <button type="button" name="upload-file" class="btn btn-primary upload-file" _mstvisible="6"
                    onclick="uploadFile()">
                    <i class="fal fa-upload" _mstvisible="7"></i>
                    Import
                </button>
            </div>
        </div>
    </div>
</form>
{literal}

    <script>
        $(document).ready(function() {
            $('#progressBar').css('display', 'none');
            var $el = $('#import_motor_part_file');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();

        });

        function _(el) {
            return document.getElementById(el);
        }

        function uploadFile() {
            var name = document.getElementById('import_motor_part_file');
            var file = name.files[0];
            var formdata = new FormData();
            formdata.append('import_motor_part_file', file);
            if ($('#delete_old').prop('checked')) {
                formdata.append("delete_old", 1);
            }
            formdata.append("uploadMotorcyclePartCSV", 1);
            formdata.append("jtl_token", $('[name="jtl_token"]').val());

            var postUrl = $('#importMotorPartCsvForm').attr('action');

            var ajax = new XMLHttpRequest();
            ajax.upload.addEventListener("progress", progressHandler, false);
            ajax.addEventListener("load", function(event) {
                _("status").innerHTML = event.target.responseText;
                _("progressBar").value = 0;
                $('#progressBar').css('display', 'none');
            }, false);
            ajax.addEventListener("error", errorHandler, false);
            ajax.addEventListener("abort", abortHandler, false);
            ajax.open("POST", postUrl);
            let response = ajax.send(formdata);
            console.log(response);
        }

        function progressHandler(event) {
            $('#progressBar').css('display', 'block');
            var name = document.getElementById('import_motor_part_file');
            var file = name.files[0];
            var _size = file.size;
            var fSExt = new Array('Bytes', 'KB', 'MB', 'GB'),
                i=0;while(_size>900){_size/=1024;i++;}
                var exactSize = (Math.round(_size * 100) / 100) + ' ' + fSExt[i];
            _("loaded_n_total").innerHTML = "Uploaded " + exactSize + " file";
            var percent = (event.loaded / event.total) * 100;
            _("progressBar").value = Math.round(percent);
            _("status").innerHTML = Math.round(percent) + "% uploaded... please wait";
        }

        function errorHandler(event) {
            _("status").innerHTML = "Upload Failed";
        }

        function abortHandler(event) {
            _("status").innerHTML = "Upload Aborted";
        }
    </script>
{/literal}

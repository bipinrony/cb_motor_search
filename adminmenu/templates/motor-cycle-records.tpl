{literal}
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function() {
            var url = $('#motorPartList').attr('action');

            setTimeout(function() {
                var count = 0;
                $('#cb_motor_part_data_table').dataTable({
                    "retrieve": true,
                    "sServerMethod": "POST",
                    "bProcessing": true,
                    ajax: {
                        url: url,
                        type: 'POST',
                        data: {'getMotorPartList' : 1, 'jtl_token': $('[name="jtl_token"]').val()}
                    },
                    "bDestroy": true,
                    "language": {
                        "emptyTable": "Keine Daten in der Tabelle verfügbar",
                        "lengthMenu": "Zeige _MENU_ Einträge",
                        "info": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen",
                        "loadingRecords": "Loading...",
                        "zeroRecords": "Keine passenden Datensätze gefunden",
                        "search": "Suche: ",
                        "paginate": {
                            "previous": "Zurück",
                            "next": "Vorwärts"
                        }
                    }
                });
            }, 300);
            // Inline editing
            var oldValue = null;
            $(document).on('dblclick', '.editable', function() {
                oldValue = $(this).html();

                $(this).removeClass('editable'); // to stop from making repeated request

                $(this).html('<input type="text" style="width:150px;" class="update" value="' + oldValue +
                    '" />');
                $(this).find('.update').focus();
            });

            var newValue = null;
            //$(document).on('blur', '.update', function(){
            $(document).on('keypress', '.update', function(e) {
                if (e.keyCode == 13) {
                    var elem = $(this);
                    newValue = $(this).val();
                    var id = $(this).parent().attr('id');
                    var colName = $(this).parent().attr('name');

                    if (newValue != oldValue) {
                        $.ajax({
                            url: url + 'list.php',
                            method: 'post',
                            data: {
                                type: 'update',
                                id: id,
                                colName: colName,
                                newValue: newValue,
                            },
                            success: function(respone) {
                                //alert(respone);
                                $(elem).parent().addClass('editable');
                                $(elem).parent().html(newValue);
                            }
                        });
                    } else {
                        $(elem).parent().addClass('editable');
                        $(this).parent().html(newValue);
                    }
                }
            });
        });
    </script>
{/literal}

<form method="post" enctype="multipart/form-data" name="motorPartList" action="{$postUrl}">
    {$jtl_token}

    <table class="list table" id="cb_motor_part_data_table" style="width: 100%;">
        <thead>
            <th class="TD1">UID</th>
            <th class="TD2">Make</th>
            <th class="TD3">Mode</th>
            <th class="TD4">Year</th>
        </thead>
        <tbody>
            <!-- Dynamic Body -->
        </tbody>
    </table>
</form>

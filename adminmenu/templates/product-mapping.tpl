{literal}
    <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function() {
            var url = $('#productMappingtList').attr('action');

            setTimeout(function() {
                var count = 0;
                $('#cb_product_mapping_data_table').dataTable({
                    "retrieve": true,
                    "sServerMethod": "POST",
                    "bProcessing": true,
                    ajax: {
                        url: url,
                        type: 'POST',
                        data: {'getproductMappingtList' : 1, 'jtl_token': $('[name="jtl_token"]').val()}
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


        });
    </script>
{/literal}

<form method="post" enctype="multipart/form-data" name="productMappingtList" action="{$postUrl}">
    {$jtl_token}

    <table class="list table" id="cb_product_mapping_data_table" style="width: 100%;">
        <thead>
            <th class="TD1">UID</th>
            <th class="TD2">ProductID</th>
            <th class="TD3">Artikelnummer</th>
        </thead>
        <tbody>
            <!-- Dynamic Body -->
        </tbody>
    </table>
</form>

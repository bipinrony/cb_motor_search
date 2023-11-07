{literal}
    <style>
        .vertical::after {
            right: 10px !important;
            top: -10px !important;
        }

        .select2-container {
            border: 1px solid #ddd;
        }

        div#result {
            height: 100%;
            background: transparent;
        }

        .part--two {width: 100%; padding: 20px 0; }
        span.select2-selection.select2-selection--single {border-radius: 3px;
        background-color: #fff;
        background-image: linear-gradient(to bottom, #fff 0%, #f8f8fa 100%);
        height: 44px;
        border: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {line-height: 44px; }
        .select-field::after, .js--fancy-select::after{display:none !important}
        .select2-container--default .select2-selection--single .select2-selection__arrow {top: 9px; right: 4px; }
        .select-field select,
        .js--fancy-select select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            padding: 0px 40px 0px 10px;
            padding: 0rem 2.5rem 0rem .625rem;
            line-height: 40px;
            line-height: 2.5rem;
            border-radius: 3px;
            background-clip: padding-box;
            background-color: #fff;
            background-image: linear-gradient(to bottom, #fff 0%, #f8f8fa 100%);
            height: auto;
            width: 100%;
            display: block;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: left;
            border: 1px solid #000000;
        }

        .select-field::after,
        .js--fancy-select::after {

            width: 2.875rem !important;
            height: 4.5rem !important;
            top: .0625rem;
            user-select: none;
            line-height: 47px !important;
            max-height: 100%;
            display: block;
            content: "\f0d7" !important;
            position: absolute;
            right: 5px !important;
            font-size: 15px !important;
            border-left: 1px solid #dadae5;
            text-align: center;
            font-family: 'FontAwesome' !important;
        }
    </style>

{/literal}
<section class="panel panel-default box box-categories word-break">
    <input type="hidden" value="{$frontend_url}" id="cb_front_url" />
    <div class="panel-heading">
        <h5 class="panel-title">
            {if $smarty.session.manufacturer != ''}
                {$langVars->getTranslation('left_search_heading')|to_charset:"UTF-8"}
            {else}
                {$langVars->getTranslation('left_search_heading')}
            {/if}
        </h5>
    </div>
    <div class="box-body">
        {if $smarty.session.manufacturer != ''}
            <div class="result--form" id="result">
                <a href="javascript:void(0)" class="destroy--ssn"
                    id="removeFilters">{$langVars->getTranslation('clear_search_button_text')|to_charset:"UTF-8"}</a>
            </div>
        {/if}
        <div class="part--two">
            <form action="#" class="ymm_search_form" id="cbMotorFilterForm" method="GET">
                <div class="row">

                    <div class="col-md-12" style="margin-bottom:10px;">
                        <div class="js--fancy-select select-field vertical">
                            <select id="cbManufacturer" class="vHersteller" name="Hersteller">
                                <option selected="true" disabled="disabled"> {$langVars->getTranslation('make')}
                                </option>
                                {foreach from=$manufacturers item=manufacturer}
                                    <option value="{$manufacturer['manufacturer']}"
                                        {if $manufacturer['manufacturer'] eq $smarty.session.manufacturer}
                                        selected="selected" {/if}>
                                        {$manufacturer['manufacturer']}
                                    {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12" style="margin-bottom:10px;">
                        <div class="js--fancy-select select-field vertical">
                            <select id="cbModel" class="vModel" name="Model">
                                <option selected="true" disabled="disabled"> {$langVars->getTranslation('model')}
                                </option>
                                {foreach from=$models item=model}
                                    <option value="{$model['model']}" {if $model['model'] eq $smarty.session.model}
                                        selected="selected" {/if}>{$model['model']}
                                    {/foreach}
                            </select>
                        </div>
                    </div>


                    <div class="col-md-12" style="margin-bottom:10px;">
                        <div class="js--fancy-select select-field vertical">

                            <select id="cbYear" class="vYear" name="Year">
                                <option selected="true" disabled="disabled">
                                    {if $smarty.session.manufacturer != ''}
                                        {$langVars->getTranslation('year')|to_charset:"UTF-8"}
                                    {else}
                                        {$langVars->getTranslation('year')}
                                    {/if}
                                </option>
                                {foreach from=$years item=year}
                                    <option value="{$year['year']}" {if $year['year'] eq $smarty.session.year}
                                        selected="selected" {/if}>{$year['year']}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {literal}
        <script>
            $(document).ready(function() {

                $(document).on('click', '#removeFilters', function() {
                    let requestData = {
                        'action': 'removeFilters'
                    };
                    cbMotorSearchAction(requestData);
                });

                $(document).on('change', '#cbManufacturer', function() {
                    let requestData = {
                        'action': "setManufacturer",
                        'manufacturer': $(this).val()
                    };
                    cbMotorSearchAction(requestData);
                });

                $(document).on('change', '#cbModel', function() {
                    let requestData = {
                        'action': "setModel",
                        'model': $(this).val()
                    };
                    cbMotorSearchAction(requestData);
                });

                $(document).on('change', '#cbYear', function() {
                    let requestData = {
                        'action': "setYear",
                        'year': $(this).val()
                    };
                    cbMotorSearchAction(requestData);
                });

            });

            function cbMotorSearchAction(requestData) {
                var cbMotorSearchUrl = $("#cb_front_url").val();
                $.ajax({
                    url: cbMotorSearchUrl,
                    data: requestData,
                    type: "POST",
                    success: function(response) {
                        if (response.flag) {
                            if (typeof response.options !== "undefined") {
                                $('#' + response.target).html(response.options);
                            }

                            var cbBaseURL = window.location.origin + window.location.pathname;

                            if (typeof response.reload !== "undefined" && response.reload) {
                                window.location.href = cbBaseURL;
                                return false;
                            }

                            if (typeof response.query_string !== "undefined") {
                                cbBaseURL = cbBaseURL + response.query_string;
                                window.location.href = cbBaseURL;
                                return false;
                            }

                        } else {
                            console.log(response);
                        }
                    }
                });
            }
        </script>
    {/literal}
</section>

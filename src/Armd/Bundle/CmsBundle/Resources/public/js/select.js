$(document).ready(function()
{
    $("select.selectUsageService").each(function () {
        setSelectUsageType(this, false);
    });

    $("select.selectUsageService").on('change', function()
    {
        setSelectUsageType(this, true);
    });

    $("select.selectUsageType").each(function () {
        setSelectUsageTypeParams(this, $("select.selectUsageService"));
    });

    $("select.selectUsageType").on('change', function()
    {
        setSelectUsageTypeParams(this, $("select.selectUsageService"));
    });

    function setSelectUsageTypeParams(object, usageService)
    {
        var filed = $("div.selectUsageParams");
        if (filed.attr('id')) {
            var usageTypeName = $(object).val(); var usageServiceName = $(usageService).val();
            var idFieldArray = filed.attr('id').split('_');
            $.ajax
            ({
                async: false,
                url: getBaseUrl() + "usageTypeParams",
                data: {
                    usageTypeName: usageTypeName,
                    usageServiceName: usageServiceName,
                    uniqid: idFieldArray[0]
                },
                success: function(jsonResponse)
                {
                    var formHtml = '';
                    if (jsonResponse.data) {
                        formHtml = jsonResponse.data.content;
                    }
                    filed.parent().html(formHtml);
                }
            });
        }
    }

    function setSelectUsageType(object, changeUsageParams)
    {
        var usageServiceName = $(object).val();

        $.ajax
        ({
            async: false,
            url: getBaseUrl() + "usageType",
            data: {
                usageServiceName: usageServiceName
            },
            success: function(jsonResponse)
            {
                responseHtml = '';
                if ( jsonResponse.data!= null ) {
                    for (var key in jsonResponse.data['choise_list']) {
                        if ( jsonResponse.data['choised'] && (key == jsonResponse.data['choised']) ) {
                            responseHtml += "<option selected='selected' value='" + key + "'>" + jsonResponse.data['choise_list'][key] + "</option>";
                        } else {
                            responseHtml += "<option value='" + key + "'>" + jsonResponse.data['choise_list'][key] + "</option>";
                        }
                    }
                }
                $("select.selectUsageType").html(responseHtml);
				$("select.selectUsageType").prev('span').html($("select.selectUsageType option:selected").text());
				
                if ( changeUsageParams == true ) {

                    $("select.selectUsageType").each(function () {
                        setSelectUsageTypeParams(this, $("select.selectUsageService"));
                    });
                }
            }
        });
    }

    function getBaseUrl()
    {
        url = selectUsageUrl.split('cms/');
        objectName = url[1].split('/');

        if ( objectName[1].match(/\d+/) == null ) {
            return url[0] + 'cms/' + objectName[0] + "/";
        }

        return url[0] + 'cms/' + objectName[0] + "/" + objectName[1] + "/";
    }
});

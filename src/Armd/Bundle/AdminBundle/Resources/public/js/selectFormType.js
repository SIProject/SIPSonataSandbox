$(document).ready(function()
{
    $("select.form_type").each(function () {
        formTypeOptions(this);
    });

    $("select.form_type").change(function()
    {
        formTypeOptions(this);
    });

    function formTypeOptions(object)
    {
        var typeName = $(object).val();
        $(".currentTypeOption").addClass('hidden');
        $("input.currentTypeOption").val(null);
        $("." + typeName + ".hidden").removeClass('hidden').addClass('currentTypeOption');
    }
});
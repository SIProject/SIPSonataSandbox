var AdminExtend = {
    add_filters: function(subject) {
        jQuery('h4.filter_legend', subject).click(function(event) {
            jQuery('div.filter_container').toggle();
        });
    }
};

$.extend(Admin, AdminExtend);
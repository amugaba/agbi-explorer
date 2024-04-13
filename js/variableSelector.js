/**
 * Add support for jQuery Select2 element.
 * Import Select2 scripts. Create select options from a list of variables.
 * Filter the target select's options whenever the category select element's value changes.
 */

var script = document.createElement("script");
script.src = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.full.js";
document.head.appendChild(script);
var link = document.createElement("link");
link.href = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css";
document.head.appendChild(link);

var variables;

function refreshVariables(category, target, allowClear) {
    $(target).val('');
    $(target).trigger('change');
    $(target).find("option:gt(0)").remove();//remove all but first option

    var categoryCode = category==null ? "" : $(category).val();

    //construct array of variables in this category
    var data = [];
    for(var i=0; i<variables.length; i++) {
        if(variables[i].category != null && (categoryCode == "" || categoryCode == variables[i].category)) {
            data.push({id: variables[i].code, text: variables[i].summary});
        }
    }
    //add variables to dropdown
    $(target).select2({data:data,
        containerCssClass: "searchbox",
        dropdownCssClass: "searchbox",
        placeholder: "Select a question",
        allowClear: allowClear
    });
}

function enableSelect2(variableList, category, target, allowClear = false) {
    variables = variableList;
    refreshVariables(category, target, allowClear);
    $(category).change(function () {
        refreshVariables(category, target, allowClear);
    });
}
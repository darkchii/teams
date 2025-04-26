var collapse_buttons = document.getElementsByClassName('collapsible');
for (var i = 0; i < collapse_buttons.length; i++) {
    collapse_buttons[i].addEventListener('click', function () {
        var for_id = this.getAttribute('for');
        var content = document.getElementById(for_id);
        const is_open = content.style.display === 'block';
        this.classList[is_open ? 'remove' : 'add']('active');
        content.style.display = is_open ? 'none' : 'block';
    });
}

//find all elements with class 'collapsible-div' and apply display none to them
var collapsible_divs = document.getElementsByClassName('collapsible-div');
for (var i = 0; i < collapsible_divs.length; i++) {
    collapsible_divs[i].style.display = 'none';
}

document.getElementById('filter-form')?.addEventListener('submit', function () {
    // Find all input elements within the form
    var inputs = this.querySelectorAll('input, textarea, select');

    console.log(inputs);

    // Loop through the inputs and disable those that are empty
    inputs.forEach(function (input) {
        //if no value, or if input has attribute exclude
        if (!input.value || input.hasAttribute('exclude')) {
            input.disabled = true;
        }

        //check if input has is_column_hider attribute
        if (input.hasAttribute('is_column_hider')) {
            //get name
            var name = input.getAttribute('name');
            var value = input.checked;

            //store in local storage
            localStorage.setItem(name, value);
        }
    });

    return true;
});

//on load
document.addEventListener('DOMContentLoaded', function () {
    //get all inputs with is_column_hider attribute
    var inputs = document.querySelectorAll('input[is_column_hider]');

    //loop through inputs
    inputs.forEach(function (input) {
        //get name
        var name = input.getAttribute('name');
        var value = localStorage.getItem(name);

        //set the checked value
        input.checked = value === 'true';

        //apply to columns
        //column name is name without '_hide'
        //find where attribute column-type is equal to column name
        var column_name = name.replace('_hide', '');
        if (active_sorter === column_name) {
            return;
        }
        var columns = document.querySelectorAll('[column-type="' + column_name + '"]');
        columns.forEach(function (column) {
            column.style.display = value === 'true' ? 'none' : '';
        });
    });
});

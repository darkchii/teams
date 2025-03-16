document.getElementById('filter-form').addEventListener('submit', function () {
    // Find all input elements within the form
    var inputs = this.querySelectorAll('input, textarea, select');

    // Loop through the inputs and disable those that are empty
    inputs.forEach(function (input) {
        //if no value, or if input has attribute exclude
        if (!input.value || input.hasAttribute('exclude')) {
            input.disabled = true;
        }

        //check if input has is_column_hider attribute
        if(input.hasAttribute('is_column_hider')){
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
document.addEventListener('DOMContentLoaded', function(){
    //get all inputs with is_column_hider attribute
    var inputs = document.querySelectorAll('input[is_column_hider]');

    //loop through inputs
    inputs.forEach(function(input){
        //get name
        var name = input.getAttribute('name');
        var value = localStorage.getItem(name);

        //set the checked value
        input.checked = value === 'true';

        //apply to columns
        //column name is name without '_hide'
        //find where attribute column-type is equal to column name
        var column_name = name.replace('_hide', '');
        if(active_sorter === column_name){
            return;
        }
        var columns = document.querySelectorAll('[column-type="' + column_name + '"]');
        columns.forEach(function(column){
            column.style.display = value === 'true' ? 'none' : '';
        });
    });
}); 
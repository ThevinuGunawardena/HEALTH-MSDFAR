var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}


$(document).ready(function () {
    // Add new row
    $('#addRow').click(function () {
        var row = $('.template').clone().removeClass('template').show();
        $('#dynamicTable tbody').append(row);
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        if ($('#dynamicTable tbody tr').length >= 2) {
            $(this).closest('tr').remove();
        } else {
            alert('At least one row is required.');
        }
    });
});
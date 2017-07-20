function Confirm(id) {
var answer = confirm("Are you sure you want to delete this Location?");
if (answer) {
window.location= '?page=locations&action=delete&id='+ id +'';
}
}

function Confirmimg(id) {
var answer = confirm("Are you sure you want to delete this Marker Image?");
if (answer) {
window.location= '?page=locations&action=deleteimg&id='+ id +'';
}
}
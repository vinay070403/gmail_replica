// document.addEventListener('DOMContentLoaded', function () {
//     const selectAll = document.getElementById('select-all');
//     const checkboxes = document.querySelectorAll('.select-mail');

//     if (selectAll) {
//         selectAll.addEventListener('change', function () {
//             checkboxes.forEach(cb => cb.checked = selectAll.checked);
//         });
//     }
// });


function toggleMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    const avatar = document.querySelector('.user-avatar');
    const dropdown = document.getElementById('userDropdown');
    if (!avatar.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

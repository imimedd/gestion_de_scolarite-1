<<<<<<< HEAD
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(function(el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.tab').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById(tab).style.display = 'block';
    event.target.classList.add('active');
=======
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(function(el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.tab').forEach(function(el) {
        el.classList.remove('active');
    });
    document.getElementById(tab).style.display = 'block';
    event.target.classList.add('active');
>>>>>>> 2932a0bf2df97e2007ef5a885fb58c4eb10562d5
}
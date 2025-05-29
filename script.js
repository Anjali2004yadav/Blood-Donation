document.getElementById("donorForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("name").value;
    const bloodGroup = document.getElementById("bloodGroup").value;
    const email = document.getElementById("email").value;

    alert(`Thank you, ${name}! Your details have been submitted.\nBlood Group: ${bloodGroup}\nEmail: ${email}`);
});

document.getElementById('logout-btn').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default anchor action

    // Simulate logout process
    setTimeout(function() {
        // Show the congratulations animation
        const congratulations = document.createElement('div');
        congratulations.className = 'congratulations';
        congratulations.innerHTML = `
            <h1>Congratulations!</h1>
            <div class="balloons"></div>
            <p>You have successfully logged out.</p>
        `;
        document.body.appendChild(congratulations);

        // Automatically remove the animation after a few seconds
        setTimeout(function() {
            congratulations.style.display = 'none';
            window.location.href = 'home.php'; // Redirect to the home page
        }, 5000); // Adjust the time if needed
    }, 1000); // Simulate the logout delay
});

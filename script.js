document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // Create form data
    const formData = new FormData();
    formData.append('username', username);
    formData.append('password', password);

    // Send POST request
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        try {
            const result = JSON.parse(data);
            if (result.success) {
                window.location.href = 'home.php';
            } else {
                document.getElementById('errorMessage').textContent = result.error || 'Login failed';
            }
        } catch (e) {
            // If the response isn't JSON, it might be a redirect
            if (data.includes('home.php')) {
                window.location.href = 'home.php';
            } else {
                document.getElementById('errorMessage').textContent = 'An error occurred';
            }
        }
    })
    .catch(error => {
        document.getElementById('errorMessage').textContent = 'Network error occurred';
    });
});
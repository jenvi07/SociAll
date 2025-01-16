// JavaScript for comment toggle in feed section
document.querySelectorAll('.comment-toggle').forEach(button => {
    button.addEventListener('click', function () {
        const commentSection = this.closest('.post').querySelector('.comment-section');
        if (commentSection.style.display === 'none' || commentSection.style.display === '') {
            commentSection.style.display = 'block';
        } else {
            commentSection.style.display = 'none';
        }
    });
});

// JavaScript for theme change
const themeToggle = document.querySelector('#theme-toggle');
themeToggle.addEventListener('change', function () {
    if (this.checked) {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
});

// Functionality for message hover to show time
document.querySelectorAll('.message').forEach(message => {
    message.addEventListener('mouseover', function () {
        const timeElement = this.querySelector('.message-time');
        timeElement.style.opacity = '1';
    });

    message.addEventListener('mouseout', function () {
        const timeElement = this.querySelector('.message-time');
        timeElement.style.opacity = '0';
    });
});

// JavaScript to hide and show navbar on hover over feed section
//const feedSection = document.querySelector('.feed-section');
//const navbar = document.querySelector('.navbar');

feedSection.addEventListener('mouseenter', () => {
    navbar.style.transform = 'translateX(-240px)';
});

feedSection.addEventListener('mouseleave', () => {
    navbar.style.transform = 'translateX(0)';
});

// Toggle Theme
function changeTheme(theme) {
    if (theme === 'dark') {
        document.body.classList.remove('light-theme');
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
        document.body.classList.add('light-theme');
    }
}

// Hide Navbar on Feed Hover
const feedSection = document.querySelector('.feed');
const navbar = document.querySelector('.navbar');

feedSection.addEventListener('mouseover', () => {
    navbar.style.display = 'none';
    feedSection.style.marginLeft = '0';
});

feedSection.addEventListener('mouseout', () => {
    navbar.style.display = 'block';
    feedSection.style.marginLeft = '17%';
});

// Hover effect for messages
const messages = document.querySelectorAll('.message');
messages.forEach((message) => {
    message.addEventListener('mouseover', () => {
        message.style.transform = 'translateX(-10%)';
        const timeElement = message.querySelector('.message-time');
        if (timeElement) timeElement.style.display = 'inline';
    });

    message.addEventListener('mouseout', () => {
        message.style.transform = 'translateX(0)';
        const timeElement = message.querySelector('.message-time');
        if (timeElement) timeElement.style.display = 'none';
    });
});

const toggleThemeButton = document.getElementById('toggle-theme');

toggleThemeButton.addEventListener('click', () => {
    document.body.classList.toggle('dark-theme');
});

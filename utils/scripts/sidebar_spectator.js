document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('groups-toggle');
    const groupsList = document.getElementById('groups-list');
    const arrow = document.getElementById('groups-arrow');

    // Function to toggle the dropdown with animation
    toggleButton.addEventListener('click', function () {
        const isHidden = groupsList.classList.contains('hidden');

        if (isHidden) {
            // Show the list
            groupsList.classList.remove('hidden');
            // Set initial height to 0 for animation
            groupsList.style.height = '0px';
            // Force a reflow to ensure the transition works
            groupsList.offsetHeight;
            // Set the height to the scroll height for the slide-down effect
            groupsList.style.height = `${groupsList.scrollHeight}px`;
            // Rotate the arrow to point up
            arrow.classList.add('rotate-180');
        } else {
            // Hide the list
            groupsList.style.height = `${groupsList.scrollHeight}px`;
            // Force a reflow
            groupsList.offsetHeight;
            // Set height to 0 for the slide-up effect
            groupsList.style.height = '0px';
            // Rotate the arrow back to point down
            arrow.classList.remove('rotate-180');
            // Add the hidden class after the animation completes
            groupsList.addEventListener('transitionend', function () {
                if (groupsList.style.height === '0px') {
                    groupsList.classList.add('hidden');
                }
            }, { once: true });
        }
    });
});
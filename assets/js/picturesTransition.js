window.addEventListener('DOMContentLoaded', function() {

    if (window.innerWidth < 1200) {

        const figures = document.querySelectorAll('#nos-cours figure');
        let currentIndex = 0;

        figures.forEach((figure, index) => {
            const img = figure.querySelector('img');
            const figcaption = figure.querySelector('figcaption');
            img.style.opacity = index === 0 ? 1 : 0;
            figcaption.style.opacity = index === 0 ? 1 : 0;
        });

        function showNextImage() {
            const currentFigure = figures[currentIndex];
            currentFigure.querySelector('img').style.opacity = 0;
            currentFigure.querySelector('figcaption').style.opacity = 0;

            currentIndex = (currentIndex + 1) % figures.length;

            const nextFigure = figures[currentIndex];
            nextFigure.querySelector('img').style.opacity = 1;
            nextFigure.querySelector('figcaption').style.opacity = 1;
        }

        setInterval(showNextImage, 3000);
    }
});
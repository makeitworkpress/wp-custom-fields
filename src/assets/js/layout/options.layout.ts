/**
 * Functions for option pages
 */
export const OptionsLayout = (framework) => {
    if (! framework.classList.contains('wpcf-options-page')) {
        return;
    }

    const scrollHeader = framework.querySelector('.wpcf-notifications') as HTMLElement;
    const scrollWidth = scrollHeader.offsetWidth;
    let scrollPosition = 0;

    window.addEventListener('scroll', () => {
        scrollPosition = window.scrollY;

        if (scrollPosition > 50) {
            scrollHeader.style.width = `${scrollWidth}px`;
            scrollHeader.closest('.wpcf-header')?.classList.add('wpfc-header-scrolling');
        } else {
            scrollHeader.closest('.wpcf-header')?.classList.remove('wpfc-header-scrolling');
        }
    });
}
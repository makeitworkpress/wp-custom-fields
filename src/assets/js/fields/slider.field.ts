export const SliderField = (framework: HTMLElement) => {
    framework.querySelectorAll('.wpcf-slider-input').forEach((slider: Element) => {
        const sliderValueElement = slider.nextElementSibling as HTMLElement;

        slider.addEventListener('input', (event: Event) => {
            sliderValueElement.innerHTML = (event?.target as HTMLInputElement)?.value;
        });
    });
}
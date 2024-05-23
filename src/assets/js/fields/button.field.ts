/**
 * The button module, accepting custom ajax actions
 * @param {HTMLElement} framework The parent framework element
 */
declare var wpcf;

export const ButtonField = (framework: HTMLElement) => {
    framework.querySelectorAll('.wpcf-button').forEach(async (button: Element) => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();

            const action = button.getAttribute('data-action');
            const data = button.getAttribute('data-data');
            const message = button.getAttribute('data-message');

            if (!action) {
                return;
            }

            const self = this;

            try {
                button.classList.add('wpcf-loading');

                const response = await fetch(wpcf.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: new URLSearchParams({
                        action: action,
                        data: data,
                        nonce: wpcf.nonce
                    })
                });
                
                console.log(response);

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const responseData = await response.json();

                if (wpcf.debug) {
                    console.log(responseData);
                }

                if (message && responseData.data !== undefined) {
                    const style = responseData.success ? 'updated' : 'error';
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('wpcf-button-message', style);
                    messageDiv.innerHTML = `<p>${responseData.data}</p>`;
                    button.after(messageDiv);

                    setTimeout(() => {
                        messageDiv.style.opacity = '0';
                    }, 3000);

                    setTimeout(() => {
                        messageDiv.remove();
                    }, 3500);                    
                }
            } catch (error) {
                if (wpcf.debug) {
                    console.error('Error:', error);
                }
            } finally {
                button.classList.remove('wpcf-loading');
            }
        });
    });
};
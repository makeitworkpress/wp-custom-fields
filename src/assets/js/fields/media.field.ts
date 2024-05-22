/**
 * Our Media upload element
 * @param {HTMLElement} framework The parent framework element
 */
declare var wp, jQuery;

export const MediaField = (framework: HTMLElement) => {
      
    const uploadWrappers = framework.querySelectorAll('.wpcf-upload-wrapper') as NodeListOf<HTMLElement>;

    uploadWrappers.forEach((uploadWrapper: HTMLElement) => {

        const addMedia = uploadWrapper.querySelector('.wpcf-upload-add') as HTMLElement;
        const addWrap = uploadWrapper.querySelector('.wpcf-single-media.empty') as HTMLElement;
        const button = uploadWrapper.dataset.button;
        const multiple = uploadWrapper.dataset.multiple === 'true';
        const title = uploadWrapper.dataset.title;
        const type = uploadWrapper.dataset.type;
        const url = uploadWrapper.dataset.url;
        const valueInput = uploadWrapper.querySelector('.wpcf-upload-value')as HTMLInputElement;
        let frame: any;

        addMedia.addEventListener('click', (e: Event) => {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: title,
                library: {
                    type: type
                },
                button: {
                    text: button
                },
                multiple: multiple
            });

            frame.on('select', () => {
                const attachments = frame.state().get('selection').toJSON();
                let attachmentIds = valueInput.value;
                let urlWrapper = '';
                let src: string;

                attachments.forEach((attachment: any) => {
                    attachmentIds += attachment.id + ',';

                    src = attachment.type === 'image' ? 
                        (attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.sizes.full.url) :
                        attachment.icon;

                    if (url) {
                        urlWrapper = '<div class="wpcf-media-url"><i class="material-icons">link</i><input type="text" value="' + attachment.url + '"></div>';
                    }

                    addWrap.insertAdjacentHTML('beforebegin', '<div class="wpcf-single-media type-' + type + '" data-id="' + attachment.id + '"><img src="' + src + '" />' + urlWrapper + '<a href="#" class="wpcf-upload-remove"><i class="material-icons">clear</i></a></div>');
                });

                if (!multiple) {
                    attachmentIds.replace(',', '');
                }

                valueInput.value = attachmentIds;
            });

            frame.open();
        });

        uploadWrapper.addEventListener('click', (event: Event) => {
            const target = event.target as HTMLElement;

            if (! target.classList.contains('wpcf-upload-remove') || target.parentElement?.classList.contains('wpcf-upload-remove') ) {
                return;
            }

            event.preventDefault();

            const singleMedia = target.closest('.wpcf-single-media') as HTMLElement;;
            const targetId = singleMedia.dataset.id;
            let currentValues = valueInput.value;
            const newValues = currentValues.replace(targetId + ',', '');

            singleMedia.remove();

            if (!multiple) {
                jQuery(addWrap).fadeIn();
            }

            valueInput.value = newValues;
            
        });
    });

    /**
     * Make media items sortable
     */
    jQuery('.wpcf-media').sortable({
        placeholder: "wpcf-media-highlight",
        update: function(event: Event, ui: any) {
            const targetElement = event.target as HTMLElement;
            const inputElement = (targetElement.closest('.wpcf-upload-wrapper') as HTMLElement).querySelector('.wpcf-upload-value') as HTMLInputElement;
            const values: string[] = [];
            
            (event.target as HTMLElement).querySelectorAll('.wpcf-single-media').forEach((node: any) => {
                values.push(node.dataset.id || '');
            });
        
            inputElement.value = values.join(',');
        }
    });
    
};
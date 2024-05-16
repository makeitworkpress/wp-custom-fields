/**
 * This script bundles all the modules from the WP_Custom_Fields Application
 * and executes them.
 */
'use strict';

import { fields } from './modules/fields';
import { options } from './modules/options';
import { tabs } from './modules/tabs';

const initWPCF = () => {
    const framework = document.querySelector('.wpcf-framework') as HTMLElement;

    if( ! framework ) {
        return;
    }

    (window as any).wpcfCodeMirror = {};

    fields(framework);    
    options(framework);
    tabs();
}

// Boot after document is loaded
document.addEventListener('DOMContentLoaded', () => initWPCF());
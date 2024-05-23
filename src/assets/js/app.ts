/**
 * This script bundles all the modules from the WP_Custom_Fields Application
 * and executes them.
 */
'use strict';

import { FieldsModule } from './modules/fields.module';
import { OptionsLayout } from './layout/options.layout';
import { TabsLayout } from './layout/tabs.layout';

const InitWPCF = () => {
    const framework = document.querySelector('.wpcf-framework') as HTMLElement ?? undefined;
    (window as any).wpcfCodeMirror = {};

    FieldsModule(framework);  
    
    if(framework) {
        OptionsLayout(framework);
    }

    TabsLayout();
}

// Boot after document is loaded
document.addEventListener('DOMContentLoaded', () => InitWPCF());
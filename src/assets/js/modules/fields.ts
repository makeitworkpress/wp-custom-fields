/**
 * Executes Field modules
 */
import { button } from './fields/button';
import { code } from './fields/code';
import { colorpicker } from './fields/colorpicker';
import { datepicker } from './fields/datepicker';
import { heading } from './fields/heading';
import { icons } from './fields/icons';
import { location } from './fields/location';
import { media } from './fields/media';
import { repeatable } from './fields/repeatable';
import { select } from './fields/select';

import { dependency } from './helpers/dependency';

export const fields = (framework: HTMLElement, isRepeatable = false) => {

    // Fields that require JS
    button(framework);
    colorpicker(framework);
    code(framework);
    datepicker(framework);
    heading(framework);
    icons(framework);
    location(framework);
    media(framework);
    select(framework);

    // Dependent fields helper
    dependency(framework); 

    // The fields function is also invoked by the repeatable field. 
    // To avoid circular dependency, only execute repeatable when not being a repeatable
    if( ! isRepeatable ) {
        repeatable(framework);
    }
    
};
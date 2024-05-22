/**
 * Executes Field modules
 */

import { ButtonField } from '../fields/button.field';
import { CodeField } from '../fields/code.field';
import { ColorpickerField } from '../fields/colorpicker.field';
import { DatepickerField } from '../fields/datepicker.field';
import { HeadingField } from '../fields/heading.field';
import { IconsField } from '../fields/icons.field';
import { LocationField } from '../fields/location.field';
import { MediaField } from '../fields/media.field';
import { RepeatableField } from '../fields/repeatable.field';
import { SelectField } from '../fields/select.field';
import { SliderField } from '../fields/slider.field';

import { DependencyHelper } from '../helpers/dependency.helper';

export const FieldsModule = (framework: HTMLElement, isRepeatable = false) => {

    // Fields that require JS
    ButtonField(framework);
    CodeField(framework);
    ColorpickerField(framework);
    DatepickerField(framework);
    HeadingField(framework);
    IconsField(framework);
    LocationField(framework);
    MediaField(framework);
    SliderField(framework);
    SelectField(framework);

    // Dependent fields helper
    DependencyHelper(framework); 

    // The fields function is also invoked by the repeatable field. 
    // To avoid circular execution, only execute repeatable when not being a repeatable
    if( ! isRepeatable ) {
        RepeatableField(framework);
    }
    
};
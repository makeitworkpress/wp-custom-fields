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

    // Those fields can also live in the customizer, without a framework element, 
    // executed after a small time-out to hack loading after customizer
    setTimeout(() => {
        HeadingField();
        SelectField();
    }, 10)
    
    // Fields that require JS
    if (! framework) {
        return;
    }

    ButtonField(framework);
    CodeField(framework);
    ColorpickerField(framework);
    DatepickerField(framework);
    IconsField(framework);
    LocationField(framework);
    MediaField(framework);
    SliderField(framework);

    // Dependent fields helper
    DependencyHelper(framework); 

    // The fields function is also invoked by the repeatable field. 
    // To avoid circular execution, only execute repeatable when not being a repeatable
    if( ! isRepeatable ) {
        RepeatableField(framework);
    }
    
};
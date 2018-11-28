import { GPlacesAutocompleteDirective } from './g-places-autocomplete.directive';
import {ElementRef} from '@angular/core';

describe('GPlacesAutocompleteDirective', () => {
  it('should create an instance', () => {
    // @ts-ignore
    const directive = new GPlacesAutocompleteDirective();
    expect(directive).toBeTruthy();
  });
});

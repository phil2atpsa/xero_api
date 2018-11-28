import { NgModule } from '@angular/core';

import { GPlacesAutocompleteDirective } from '../directives/g-places-autocomplete.directive';
import {AgmCoreModule} from '@agm/core';


@NgModule({
  imports: [
    AgmCoreModule

  ],
  declarations: [
    GPlacesAutocompleteDirective
  ],
  exports: [
    AgmCoreModule, GPlacesAutocompleteDirective
  ]
})
export class SharedModule { }

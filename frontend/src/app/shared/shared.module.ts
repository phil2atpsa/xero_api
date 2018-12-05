import { NgModule } from '@angular/core';

import { GPlacesAutocompleteDirective } from '../directives/g-places-autocomplete.directive';
import {AgmCoreModule} from '@agm/core';
import {AccountingModule} from "../views/accounting/accounting.module";



@NgModule({
  imports: [
    AgmCoreModule

  ],
  declarations: [
    GPlacesAutocompleteDirective
  ],
  exports: [
    AgmCoreModule, GPlacesAutocompleteDirective,AccountingModule
  ]
})
export class SharedModule { }

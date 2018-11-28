import {Directive, ElementRef, OnInit, NgZone, EventEmitter, Output} from '@angular/core';
// @ts-ignore
import { } from 'googlemaps';
import { MapsAPILoader } from '@agm/core';
import {NgModel} from '@angular/forms';



@Directive({
  selector: '[appGPlacesAutocomplete]',
  providers: [NgModel],
  host: {
    '(input)' : 'onInputChange()'
  }
})
export class GPlacesAutocompleteDirective implements OnInit  {


  @Output() setAddress: EventEmitter<any> = new EventEmitter();
  modelValue: any;


  private element: HTMLInputElement;
  public latitude: number;
  public longitude: number;
  public formatted_address: string;

  constructor(private elRef: ElementRef, private ngZone: NgZone, private model: NgModel) {
    this.element = elRef.nativeElement;
    this.modelValue = this.model;
  }

  ngOnInit(): void {
    const autocomplete = new google.maps.places.Autocomplete(this.element);
    autocomplete.addListener('place_changed', () => {
      this.ngZone.run(() => {
        const place: google.maps.places.PlaceResult = autocomplete.getPlace();
        if (place.geometry === undefined || place.geometry === null) {
          return;
        }
        this.invokeEvent(place);

        this.latitude = place.geometry.location.lat();
        this.longitude = place.geometry.location.lng();
        this.formatted_address  = place.formatted_address;
        this.element.value = this.formatted_address;
      });
    });
  }

  invokeEvent(place: Object) {
    this.setAddress.emit(place);
  }

  onInputChange() {
  //  console.log(this.model);
  }


}

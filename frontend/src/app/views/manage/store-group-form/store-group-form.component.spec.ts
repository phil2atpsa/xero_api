import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StoreGroupFormComponent } from './store-group-form.component';

describe('StoreGroupFormComponent', () => {
  let component: StoreGroupFormComponent;
  let fixture: ComponentFixture<StoreGroupFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StoreGroupFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StoreGroupFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

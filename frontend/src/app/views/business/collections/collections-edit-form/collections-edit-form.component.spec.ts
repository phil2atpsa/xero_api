import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CollectionsEditFormComponent } from './collections-edit-form.component';

describe('CollectionsEditFormComponent', () => {
  let component: CollectionsEditFormComponent;
  let fixture: ComponentFixture<CollectionsEditFormComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CollectionsEditFormComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CollectionsEditFormComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

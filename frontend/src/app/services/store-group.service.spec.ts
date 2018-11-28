import { TestBed } from '@angular/core/testing';

import { StoreGroupService } from './store-group.service';

describe('StoreGroupService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: StoreGroupService = TestBed.get(StoreGroupService);
    expect(service).toBeTruthy();
  });
});

import { TestBed } from '@angular/core/testing';

import { UnallocatedService } from './unallocated.service';

describe('UnallocatedService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: UnallocatedService = TestBed.get(UnallocatedService);
    expect(service).toBeTruthy();
  });
});

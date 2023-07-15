import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateannouncementComponent } from './updateannouncement.component';

describe('UpdateannouncementComponent', () => {
  let component: UpdateannouncementComponent;
  let fixture: ComponentFixture<UpdateannouncementComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ UpdateannouncementComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UpdateannouncementComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
